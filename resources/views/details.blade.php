@extends('layouts.app')
@section('content')
<style>
    .filled-heart {
        color: red;
    }
</style>

<main class="pt-90">
    <div class="mb-md-1 pb-md-3"></div>
    @if(session('error'))
    <div class="container mt-3">
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
    @endif
    <section class="product-single container">
        <div class="row">
            <div class="col-lg-7">
                <div class="product-single__media" data-media-type="vertical-thumbnail">
                    <div class="product-single__image">
                        <div class="swiper-container">
                            <div class="swiper-wrapper">
                                <div class="swiper-slide product-single__image-item">
                                    <img loading="lazy" class="h-auto" src="{{asset('uploads/products')}}/{{$product->image}}" width="674" height="674" alt="" />
                                    <a data-fancybox="gallery" href="{{asset('uploads/products')}}/{{$product->image}}" data-bs-toggle="tooltip" data-bs-placement="left" title="Zoom">
                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <use href="#icon_zoom" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="product-single__thumbnail">
                        <div class="swiper-container">
                            <div class="swiper-wrapper">
                                <div class="swiper-slide product-single__image-item"><img loading="lazy" class="h-auto" src="{{asset('uploads/products/thumbnails')}}/{{$product->image}}" width="104" height="104" alt="" /></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="d-flex justify-content-between mb-4 pb-md-2">
                    <div class="breadcrumb mb-0 d-none d-md-block flex-grow-1">
                        <a href="#" class="menu-link menu-link_us-s text-uppercase fw-medium">Home</a>
                        <span class="breadcrumb-separator menu-link fw-medium ps-1 pe-1">/</span>
                        <a href="#" class="menu-link menu-link_us-s text-uppercase fw-medium">The Shop</a>
                    </div>
                </div>
                <h1 class="product-single__name">{{$product->name}}</h1>
                <div class="product-single__price">
                    <span class="current-price">
                        @if($product->sale_price)
                        <!-- <s>${{$product->regular_price}}</s> --> ₱{{$product->sale_price}}
                        @else
                        ₱{{$product->sale_price}}
                        @endif
                    </span>
                </div>
                <div class="product-single__short-desc">
                    <p>{{$product->short_description}}</p>
                </div>
                @php
                $wishlistInstance = Cart::instance('wishlist');
                $item = $wishlistInstance->content()->where('id', $product->id)->first();
                @endphp

                @if ($product->stock_status == 'outofstock')
                @if ($item)
                <!-- Form to remove from wishlist -->
                <form method="POST" action="{{ route('wishlist.item.remove', ['rowId' => $item->rowId]) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger" title="Remove from Wishlist">
                        <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <use href="#icon_heart" />
                        </svg>
                        <span>Remove from Wishlist</span>
                    </button>
                </form>
                @else
                <form method="POST" action="{{ route('wishlist.add') }}">
                    @csrf
                    <input type="hidden" name="id" value="{{ $product->id }}">
                    <input type="hidden" name="name" value="{{ $product->name }}">
                    <input type="hidden" name="price" value="{{ $product->sale_price }}">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" class="btn btn-primary" title="Add to Wishlist">
                        <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <use href="#icon_heart" />
                        </svg>
                        <span>Add to Wishlist</span>
                    </button>
                </form>
                @endif
                @else
                @if (Cart::instance('cart')->content()->where('id', $product->id)->count() > 0)
                <a href="{{ route('cart.index') }}" class="btn btn-warning">Go to Cart</a>
                @else
                <form name="addtocart-form" method="POST" action="{{ route('cart.add') }}">
                    @csrf
                    <input type="hidden" name="id" value="{{ $product->id }}">

                    @if($product->colors && count($product->colors) > 0)
                    <div class="mb-3">
                        <div class="fw-medium mb-2">Bead Color <span class="text-danger">*</span></div>
                        <div class="d-flex flex-wrap gap-2" id="color-options">
                            @foreach($product->colors as $color)
                            <label class="bead-option-label">
                                <input type="radio" name="color" value="{{ $color }}" class="bead-radio" required>
                                <span class="bead-option-pill">{{ $color }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if($product->sizes && count($product->sizes) > 0)
                    <div class="mb-3">
                        <div class="fw-medium mb-2">Size <span class="text-danger">*</span></div>
                        <div class="d-flex flex-wrap gap-2" id="size-options">
                            @foreach($product->sizes as $size)
                            <label class="bead-option-label">
                                <input type="radio" name="size" value="{{ $size }}" class="bead-radio" required>
                                <span class="bead-option-pill">{{ $size }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <div class="mb-3">
                        <div class="fw-medium mb-2">Special Instructions <span class="text-muted" style="font-weight:400;font-size:.85rem;">(optional)</span></div>
                        <textarea name="instructions" class="form-control" rows="2" maxlength="300"
                            placeholder="e.g. gift wrapping, specific pattern, name to bead-spell..."></textarea>
                    </div>

                    <div class="product-single__addtocart">
                        <div class="qty-control position-relative">
                            <input type="number" name="quantity" value="1" min="1" class="qty-control__number text-center">
                            <div class="qty-control__reduce">-</div>
                            <div class="qty-control__increase">+</div>
                        </div>
                        <button type="submit" class="btn btn-primary">Add to Cart</button>
                    </div>
                </form>

                <style>
                    .bead-option-label { cursor: pointer; }
                    .bead-radio { display: none; }
                    .bead-option-pill {
                        display: inline-block;
                        padding: 6px 16px;
                        border: 2px solid #ccc;
                        border-radius: 30px;
                        font-size: .875rem;
                        transition: all .15s ease;
                        user-select: none;
                    }
                    .bead-radio:checked + .bead-option-pill {
                        border-color: #b08968;
                        background: #b08968;
                        color: #fff;
                    }
                    .bead-option-pill:hover { border-color: #b08968; }
                </style>
                @endif
                @endif
                <div class="product-single__meta-info">
                    <div class="meta-item">
                        <label>Category:</label>
                        <span>{{$product->category->name}}</span>
                    </div>
                </div>
                <share-button class="share-button">
                    <button class="menu-link menu-link_us-s to-share border-0 bg-transparent d-flex align-items-center">
                        <svg width="16" height="19" viewBox="0 0 16 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <use href="#icon_sharing" />
                        </svg>
                        <span>Share</span>
                    </button>
                    <details id="Details-share-template__main" class="m-1 xl:m-1.5" hidden="">
                        <summary class="btn-solid m-1 xl:m-1.5 pt-3.5 pb-3 px-5">+</summary>
                        <div id="Article-share-template__main" class="share-button__fallback flex items-center absolute top-full left-0 w-full px-2 py-4 bg-container shadow-theme border-t z-10">
                            <div class="field grow mr-4">
                                <label class="field__label sr-only" for="url">Link</label>
                                <input type="text" class="field__input w-full" id="url" value="https://uomo-crystal.myshopify.com/blogs/news/go-to-wellness-tips-for-mental-health" placeholder="Link" onclick="this.select();" readonly="">
                            </div>
                            <button class="share-button__copy no-js-hidden">
                                <svg class="icon icon-clipboard inline-block mr-1" width="11" height="13" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false" viewBox="0 0 11 13">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M2 1a1 1 0 011-1h7a1 1 0 011 1v9a1 1 0 01-1 1V1H2zM1 2a1 1 0 00-1 1v9a1 1 0 001 1h7a1 1 0 001-1V3a1 1 0 00-1-1H1zm0 10V3h7v9H1z" fill="currentColor"></path>
                                </svg>
                                <span class="sr-only">Copy link</span>
                            </button>
                        </div>
                    </details>
                </share-button>
                <script src="js/details-disclosure.js" defer="defer"></script>
                <script src="js/share.js" defer="defer"></script>
            </div>
        </div>
        </div>
        <div class="product-single__details-tab">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link nav-link_underscore active" id="tab-description-tab" data-bs-toggle="tab" href="#tab-description" role="tab" aria-controls="tab-description" aria-selected="true">Description</a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade show active" id="tab-description" role="tabpanel" aria-labelledby="tab-description-tab">
                    <div class="product-single__description text-center">
                        {{$product->description}}
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="products-carousel container">
        <h2 class="h3 text-uppercase mb-4 pb-xl-2 mb-xl-4">Related <strong>Products</strong></h2>

        <div id="related_products" class="position-relative">
            <div class="swiper-container js-swiper-slider" data-settings='{
        "autoplay": false,
        "slidesPerView": 4,
        "slidesPerGroup": 4,
        "effect": "none",
        "loop": true,
        "pagination": {
          "el": "#related_products .products-pagination",
          "type": "bullets",
          "clickable": true
        },
        "navigation": {
          "nextEl": "#related_products .products-carousel__next",
          "prevEl": "#related_products .products-carousel__prev"
        },
        "breakpoints": {
          "320": {
            "slidesPerView": 2,
            "slidesPerGroup": 2,
            "spaceBetween": 14
          },
          "768": {
            "slidesPerView": 3,
            "slidesPerGroup": 3,
            "spaceBetween": 24
          },
          "992": {
            "slidesPerView": 4,
            "slidesPerGroup": 4,
            "spaceBetween": 30
          }
        }
      }'>
                <div class="swiper-wrapper">
                    @foreach ($rproducts as $rproduct)
                    <div class="swiper-slide product-card">
                        <div class="pc__img-wrapper">
                            <a href="{{route('shop.product.details',['product_slug'=>$rproduct->slug])}}">
                                <img loading="lazy" src="{{asset('uploads/products')}}/{{$rproduct->image}}" width="330" height="400"
                                    alt="{{$rproduct->name}}" class="pc__img">
                            </a>
                            @php
                            $wishlist = Cart::instance('wishlist');
                            $wishlistItem = $wishlist->content()->where('id', $rproduct->id)->first();
                            @endphp
                            @if($rproduct->stock_status == 'outofstock')
                            @if($wishlistItem)
                            <a href="{{route('wishlist.index')}}" class="pc__atc btn anim_appear-bottom btn position-absolute border-0 text-uppercase fw-medium btn-warning mb-3">Go to Wishlist</a>
                            @else
                            <form method="POST" action="{{route('wishlist.add')}}">
                                @csrf
                                <input type="hidden" name="id" value="{{$rproduct->id}}">
                                <input type="hidden" name="name" value="{{$rproduct->name}}">
                                <input type="hidden" name="price" value="{{$rproduct->sale_price}}">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="pc__atc btn anim_appear-bottom btn position-absolute border-0 text-uppercase fw-medium"
                                    title="Add To Wishlist">Add To Wishlist</button>
                            </form>
                            @endif
                            @else
                            @if($wishlistItem)
                            <a href="{{route('wishlist.index')}}" class="pc__atc btn anim_appear-bottom btn position-absolute border-0 text-uppercase fw-medium btn-warning mb-3">Go to Wishlist</a>
                            @elseif(Cart::instance('cart')->content()->where('id', $rproduct->id)->count()>0)
                            <a href="{{route('cart.index')}}" class="pc__atc btn anim_appear-bottom btn position-absolute border-0 text-uppercase fw-medium btn-warning mb-3">Go to Cart</a>
                            @else
                            <form name="addtocart-form" method="post" action="{{route('cart.add')}}">
                                @csrf
                                <input type="hidden" name="id" value="{{$rproduct->id}}">
                                <input type="hidden" name="quantity" value="1">
                                <input type="hidden" name="name" value="{{$rproduct->name}}">
                                <input type="hidden" name="price" value="{{$rproduct->sale_price}}">
                                <button type="submit" class="pc__atc btn anim_appear-bottom btn position-absolute border-0 text-uppercase fw-medium"
                                    title="Add To Cart">Add To Cart</button>
                            </form>
                            @endif
                            @endif
                        </div>

                        <div class="pc__info position-relative">
                            <p class="pc__category">{{$rproduct->category->name}}</p>
                            <h6 class="pc__title"><a href="{{route('shop.product.details',['product_slug'=>$rproduct->slug])}}">{{$rproduct->name}}</a></h6>
                            <div class="product-card__price d-flex">
                                <span class="money price">
                                    ₱{{$rproduct->sale_price ? $rproduct->sale_price : $rproduct->regular_price}}
                                </span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="products-pagination mt-4 mb-5 d-flex align-items-center justify-content-center"></div>
        </div>

    </section>
</main>
@endsection