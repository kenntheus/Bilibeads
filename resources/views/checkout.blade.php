@extends('layouts.app')
@section('content')
<main class="pt-90">
    <div class="mb-4 pb-4"></div>
    <section class="shop-checkout container">
        <h2 class="page-title">Shipping and Checkout</h2>
        <div class="checkout-steps">
            <a href="{{ route('cart.index') }}" class="checkout-steps__item active">
                <span class="checkout-steps__item-number">01</span>
                <span class="checkout-steps__item-title">
                    <span>Shopping Bag</span>
                    <em>Manage Your Items List</em>
                </span>
            </a>
            <a href="javascript:void(0)" class="checkout-steps__item active">
                <span class="checkout-steps__item-number">02</span>
                <span class="checkout-steps__item-title">
                    <span>Shipping and Checkout</span>
                    <em>Checkout Your Items List</em>
                </span>
            </a>
            <a href="javascript:void(0)" class="checkout-steps__item">
                <span class="checkout-steps__item-number">03</span>
                <span class="checkout-steps__item-title">
                    <span>Confirmation</span>
                    <em>Review And Submit Your Order</em>
                </span>
            </a>
        </div>

        <form name="checkout-form" action="{{ route('cart.place.an.order') }}" method="POST">
            @csrf
            <div class="checkout-form">
                <div class="billing-info__wrapper">
                    <div class="row">
                        <div class="col-12">
                            <h4>SHIPPING DETAILS</h4>
                        </div>
                    </div>

                    @if($address)
                    <div class="row" id="currentAddress">
                        <div class="col-md-12">
                            <div class="my-account__address-list">
                                <div class="my-account__address-list-item">
                                    <div class="my-account__address-item__detail">
                                        <p>{{$address->name}}</p>
                                        <p>{{$address->address}}</p>
                                        <p>{{$address->city}}, {{$address->state}}, {{$address->country}}</p>
                                        <p>{{$address->zip}}</p>
                                        <br>
                                        <p>{{$address->phone}}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    <div class="row mt-3 @if($address) d-none @endif" id="addressForm">
                        <div class="col-md-6">
                            <div class="form-group my-3">
                                <label for="name">Full Name *</label>
                                <input type="text" class="form-control" name="name" required
                                    value="{{ $address ? $address->name : old('name') }}">
                                @error('name')<span class="text-danger">{{$message}}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group my-3">
                                <label for="phone">Phone Number *</label>
                                <input type="text" class="form-control" name="phone" required
                                    value="{{ $address ? $address->phone : old('phone') }}">
                                @error('phone')<span class="text-danger">{{$message}}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group my-3">
                                <label for="state">Province *</label>
                                <select name="state" class="form-control" required>
                                    <option value="">Select Province</option>
                                </select>
                                @error('state')<span class="text-danger">{{$message}}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group my-3">
                                <label for="city">Town / City *</label>
                                <select name="city" class="form-control" required>
                                    <option value="">Select City</option>
                                </select>
                                @error('city')<span class="text-danger">{{$message}}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group my-3">
                                <label for="barangay">Barangay *</label>
                                <select name="barangay" class="form-control" required>
                                    <option value="">Select Barangay</option>
                                </select>
                                @error('barangay')<span class="text-danger">{{$message}}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group my-3">
                                <label for="address">House no, Building Name *</label>
                                <input type="text" class="form-control" name="address" required
                                    value="{{ $address ? $address->address : old('address') }}">
                                @error('address')<span class="text-danger">{{$message}}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group my-3">
                                <label for="zip">Zip Code *</label>
                                <input type="text" class="form-control" name="zip" required
                                    value="{{ $address ? $address->zip : old('zip') }}">
                                @error('zip')<span class="text-danger">{{$message}}</span> @enderror
                            </div>
                        </div>
                    </div>

                    @if($address)
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <button type="button" id="editAddressBtn" class="btn btn-primary">Edit Address</button>
                        </div>
                    </div>
                    @endif
                </div>

                <div class="checkout__totals-wrapper">
                    <div class="sticky-content">
                        <div class="checkout__totals">
                            <h3>Your Order</h3>
                            <table class="checkout-cart-items">
                                <thead>
                                    <tr>
                                        <th>PRODUCT</th>
                                        <th align="right">
                                            {{ Cart::instance('cart')->count() }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach (Cart::instance('cart') as $item)
                                    <tr>
                                        <td>{{ $item->name }} x {{ $item->qty }}</td>
                                        <td align="right">{{ $item->subtotal() }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <table class="checkout-totals">
                                <tbody>
                                    <tr>
                                        <th>SUBTOTAL</th>
                                        <td align="right">₱{{ Cart::instance('cart')->subtotal() }}</td>
                                    </tr>
                                    <tr>
                                        <th>SHIPPING FEE</th>
                                        <td align="right">₱{{ Cart::instance('cart')->tax() }}</td>
                                    </tr>
                                    <tr>
                                        <th>TOTAL</th>
                                        <td align="right">₱{{ Cart::instance('cart')->total() }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="checkout__payment-methods">
                            <div class="form-check">
                                <h4>Payment Method:</h4>
                                <br>
                                <input class="form-check-input form-check-input_fill" type="radio" name="mode" id="mode3" value="cod" required>
                                <label class="form-check-label" for="mode3">Cash on Delivery</label>
                            </div>
                        </div>
                        <button class="btn btn-primary btn-checkout">PLACE ORDER</button>
                    </div>
                </div>
            </div>
        </form>
    </section>
</main>
<input type="hidden" id="addressData" value="{{ json_encode($address)}}">

<script>
    try {
        var addressDataElement = document.getElementById('addressData');
        var address = JSON.parse(addressDataElement.value);
        console.log('Address Data:', address);
    } catch (e) {
        console.error('Error parsing JSON:', e);
    }
    document.getElementById('editAddressBtn')?.addEventListener('click', function() {
        var addressForm = document.getElementById('addressForm');
        var currentAddress = document.getElementById('currentAddress');
        var editButton = document.getElementById('editAddressBtn');

        if (currentAddress) {
            currentAddress.style.display = 'none';
        }
        addressForm.classList.remove('d-none');
        editButton.style.display = 'none';
    });

    let provinces = [];
    let cities = [];
    let barangays = [];

    fetch('/assets/ph-province-list.json')
        .then(response => response.json())
        .then(data => {
            provinces = data;
            populateProvinces();
            console.log('Provinces fetched', provinces);
        });

    fetch('/assets/ph-cities-list.json')
        .then(response => response.json())
        .then(data => {
            cities = data;
            if(address){
                populateCities(address.state);
            }
            console.log('Cities fetched', cities);
        });

    fetch('/assets/ph-brgy-list.json')
        .then(response => response.json())
        .then(data => {
            barangays = data;
                
            if(address){
                populateBarangays(address.city);
            }
            console.log('Barangays fetched', barangays);
        });

    function populateProvinces() {
        const provinceSelect = document.querySelector('select[name="state"]');
        
        provinceSelect.innerHTML = '<option value="">Select Province</option>';
        console.log("province testses", provinces)
        console.log(address)
        provinces.forEach(province => {
            const option = document.createElement('option');
            option.text = province.province;
            
            if(address && address.state === province.province){
                option.selected = true;
            }
            provinceSelect.add(option);
        });
    }
    document.querySelector('select[name="state"]').addEventListener('change', function() {
        const selectedProvince = this.value;
        console.log('Province selected:', selectedProvince);
        populateCities(selectedProvince);
    });

    document.querySelector('select[name="city"]').addEventListener('change', function() {
        const selectedCity = this.value;
        console.log('City selected:', selectedCity);
        populateBarangays(selectedCity);
    });
    function populateCities(province) {
        const citySelect = document.querySelector('select[name="city"]');
        citySelect.innerHTML = '<option value="">Select City</option>';
        const filteredCities = cities.filter(city => city.province === province);
        filteredCities.forEach(city => {
            const option = document.createElement('option');
            option.text = city.city;
            if(address && address.city === city.city){
                option.selected = true;
            }
            citySelect.add(option);
        });
        const barangaySelect = document.querySelector('select[name="barangay"]');
        if(address){
            provinceSelect.innerHTML = `<option value="" Selected>${address.barangay}</option>`;
        }
        barangaySelect.innerHTML = '<option value="">Select Barangay</option>';
    }
    function populateBarangays(city) {
        const barangaySelect = document.querySelector('select[name="barangay"]');
        barangaySelect.innerHTML = '<option value="">Select Barangay</option>';
        const filteredBarangays = barangays.filter(brgy => brgy.municipality === city);

        filteredBarangays.forEach(barangay => {
            const option = document.createElement('option');
            option.text = barangay.barangay;
            if(address && address.barangay === barangay.barangay){
                option.selected = true;
            }
            barangaySelect.add(option);
        });
    }
</script>
@endsection