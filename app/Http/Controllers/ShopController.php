<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $size = $request->query('size') ? $request->query('size') : 12;
        $o_column = "";
        $_order = "";
        $order = $request->query('order') ? $request->query('order') : -1;
        $f_categories = $request->query('categories');
        $min_price = $request->query('min') ? $request->query('min') : 1;
        $max_price = $request->query('max') ? $request->query('max') : 500;
        switch($order){
            case 1:
                $o_column='created_at';
                $o_order='DESC';
                break;
            case 2:
                $o_column='created_at';
                $o_order='ASC';
                break;
            case 3:
                $o_column='sale_price';
                $o_order='ASC';
                break;
            case 4:
                $o_column='sale_price';
                $o_order='DESC';
                break;
            default:
                $o_column = 'id';
                $o_order = 'DESC';
        }
        $categories = Category::orderBy('name','ASC')->get();
        $products = Product::where(function($query) use($f_categories){
            $query->whereIn('category_id',explode(',',$f_categories))->orWhereRaw('? = \'\'', [$f_categories]);
        })

        ->where(function($query) use($min_price,$max_price){
            $query->whereBetween('sale_price',[$min_price,$max_price]);
        })
        
                        ->orderBy($o_column,$o_order)->paginate($size);
        return view('shop',compact('products','size','order','categories','f_categories','min_price','max_price'));
    }

    public function product_details($product_slug)
    {
        $product = Product::where('slug',$product_slug)->first();
        $rproducts = Product::where('slug','<>',$product_slug)->get()->take(8);
        return view('details',compact('product','rproducts'));
    }
}
