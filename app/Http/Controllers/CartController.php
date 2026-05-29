<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Surfsidemedia\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // Apply auth middleware to all actions
    }

    public function index()
    {
        $items = Cart::instance('cart')->content();
        return view('cart', compact('items'));
    }

    public function add_to_cart(Request $request)
    {
        $product = Product::findOrFail($request->id);

        if ($product->stock_status === 'outofstock' || $product->quantity < 1) {
            return redirect()->back()->with('error', 'This product is out of stock.');
        }

        $requestedQty = max(1, (int) $request->quantity);
        $existingItem = Cart::instance('cart')->content()->where('id', $product->id)->first();
        $currentCartQty = $existingItem ? $existingItem->qty : 0;

        if ($currentCartQty + $requestedQty > $product->quantity) {
            return redirect()->back()->with('error', "Only {$product->quantity} item(s) available. You already have {$currentCartQty} in your cart.");
        }

        $options = [];
        if ($request->filled('color')) $options['color'] = $request->color;
        if ($request->filled('size')) $options['size'] = $request->size;
        if ($request->filled('instructions')) $options['instructions'] = $request->instructions;

        Cart::instance('cart')->add($product->id, $product->name, $requestedQty, $product->sale_price, $options)
            ->associate('App\Models\Product');
        return redirect()->back();
    }

    public function increase_item_quantity(string $rowId)
    {
        $cartItem = Cart::instance('cart')->get($rowId);
        $product = $cartItem->model;

        if ($cartItem->qty + 1 > $product->quantity) {
            return redirect()->back()->with('error', "Only {$product->quantity} item(s) available in stock.");
        }

        Cart::instance('cart')->update($rowId, $cartItem->qty + 1);
        return redirect()->back();
    }

    public function reduce_item_quantity(string $rowId)
    {
        $cartItem = Cart::instance('cart')->get($rowId);
        Cart::instance('cart')->update($rowId, $cartItem->qty - 1);
        return redirect()->back();
    }

    public function remove_item(string $rowId)
    {
        Cart::instance('cart')->remove($rowId);
        return redirect()->back();
    }

    public function empty_cart()
    {
        Cart::instance('cart')->destroy();
        return redirect()->back();
    }

    public function checkout()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $address = Address::where('user_id', Auth::user()->id)->orderBy('created_at', 'desc')->first();

        return view('checkout', compact('address'));
    }

    public function place_an_order(Request $request)
    {
        $user_id = Auth::user()->id;
        $address = Address::where('user_id', $user_id)->where('isdefault', true)->first();

        if (!$address || $request->has('name')) {
            $request->validate([
                'name' => 'required|max:100',
                'phone' => ['required', 'regex:/^\+63[0-9]{10}$/'],
                'state' => 'required',
                'city' => 'required',
                'barangay' => 'required',
                'zip' => 'required|numeric|digits:4',
            ]);

            if (!$address) {
                $address = new Address();
                $address->user_id = $user_id;
            }

            $address->name = $request->name;
            $address->phone = $request->phone;
            $address->state = $request->state;
            $address->city = $request->city;
            $address->barangay = $request->barangay;
            $address->zip = $request->zip;
            $address->address = $request->address;
            $address->country = 'Philippines';
            $address->isdefault = true;
            $address->save();
        }

        $this->setAmountforCheckout();

        $order = new Order();
        $order->user_id = $user_id;
        $order->subtotal = Session::get('checkout')['subtotal'];
        $order->tax = Session::get('checkout')['tax'];
        $order->total = Session::get('checkout')['total'];
        $order->name = $address->name;
        $order->phone = $address->phone;
        $order->address = $address->address;
        $order->state = $address->state;
        $order->city = $address->city;
        $order->barangay = $request->barangay;
        $order->zip = $address->zip;
        $order->country = $address->country;
        $order->save();

        foreach (Cart::instance('cart')->content() as $item) {
            $orderItem = new OrderItem();
            $orderItem->product_id = $item->id;
            $orderItem->order_id = $order->id;
            $orderItem->price = $item->price;
            $orderItem->quantity = $item->qty;
            $orderItem->options = $item->options->count() > 0 ? $item->options->toArray() : null;
            $orderItem->save();

            $product = Product::find($item->id);
            if ($product) {
                $product->quantity = max(0, $product->quantity - $item->qty);
                if ($product->quantity === 0) {
                    $product->stock_status = 'outofstock';
                }
                $product->save();
            }
        }
        if ($request->mode == "cod") {
            $transaction = new Transaction();
            $transaction->user_id = $user_id;
            $transaction->order_id = $order->id;
            $transaction->mode = $request->mode;
            $transaction->save();
        }

        Cart::instance('cart')->destroy();
        DB::table('user_cart')->where('user_id', Auth::id())->delete();
        Session::forget('checkout');
        Session::put('order_id', $order->id);

        return redirect()->route('cart.order.confirmation');
    }

    public function setAmountforCheckout()
    {
        if (!Cart::instance('cart')->content()->count() > 0) {
            Session::forget('checkout');
            return;
        } else {
            Session::put('checkout', [
                'subtotal' => Cart::instance('cart')->subtotal(),
                'tax' => Cart::instance('cart')->tax(),
                'total' => Cart::instance('cart')->total(),
            ]);
        }
    }

    public function order_confirmation()
    {
        if (Session::has('order_id')) {
            $order = Order::find(Session::get('order_id'));
            return view('order-confirmation', compact('order'));
        }
        return redirect()->route('cart.index');
    }

    public function saveCartToDatabase()
    {
        $user_id = Auth::id();
        foreach (Cart::instance('cart')->content() as $item) {
            DB::table('user_cart')->updateOrInsert(
                ['user_id' => $user_id, 'product_id' => $item->id],
                [
                    'name'          => $item->name,
                    'quantity'      => $item->qty,
                    'price'         => $item->price,
                    'customization' => $item->options->count() > 0 ? json_encode($item->options->toArray()) : null,
                ]
            );
        }
    }

    public function empty_cart_on_logout()
    {
        $this->saveCartToDatabase();
        Cart::instance('cart')->destroy();
    }

    public function loadCartFromDatabase()
    {
        $user_id = Auth::id();
        $savedCartItems = DB::table('user_cart')->where('user_id', $user_id)->get();

        foreach ($savedCartItems as $item) {
            $options = $item->customization ? json_decode($item->customization, true) : [];
            Cart::instance('cart')->add($item->product_id, $item->name, $item->quantity, $item->price, $options)
                ->associate('App\Models\Product');
        }
    }
}
