<!DOCTYPE html>
<html lang="{{ $locale }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ __('Shop') }}</title>
    <meta name="description" content="{{ __('Shop products') }}">

    @include("{$template_view}.global-head")    
</head>

<body>

    <div id="all">

        <div id="content-wrap">

            @include("{$template_view}.navigation")

            @include("{$template_view}.blocks.search-cart")                 

            <section>               

                <div class="container">
                    
                    <div class="row">

                        <div class="col-12">

                            <h3>{{ __('Products categories') }}</h3>

                            <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4">
                                @foreach(cart_categ_tree($categ_id = null) as $level0)
                                <div class="col mb-4">
                                    <div class="card h-100 card-categ-grid bg-white">
                                        <div class="card-body card-categ-grid-body">
                                            @if($level0->icon)
                                            <div class="icon">
                                                {!! $level0->icon !!}
                                            </div>
                                            @endif
                                            <div class="card-categ-grid-text">
                                                <a href="{{ cart_url($level0->id) }}">{{ $level0->title }}</a>
                                                <div class="mt-1"></div>
                                                {{ $level0->count_tree_items }} {{ __('products') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                    </div>
                </div>

            </section>


            <section class="bar background-white no-mb min_height">

                <div class="container">

                    <div class="row">

                        <div class="col-12">
                            <h3>{{ __('Featured products') }}</h3>
                            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4">
                                @foreach (cart_products(null, 'featured_only') as $product)
                                <div class="col mb-4">
                                    <div class="card h-100 card-product-grid">
                                        <div class="card-header card-product-grid-image">
                                            <a href="{{ cart_product_url($product->id) }}">
                                                <img src="@if($product->image){{ thumb($product->image) }} @else {{ asset('assets/img/no-image.png') }} @endif" class="img-fluid"
                                                    alt="{{ $product->title }}">
                                            </a>
                                        </div>
                                        <div class="card-body card-product-grid-body">
                                            <div class="card-product-grid-title">
                                                <a title="{{ $product->title }}" href="{{ cart_product_url($product->id) }}">{{ $product->title }}</a>
                                            </div>
                                            <div class="card-product-grid-price">{{ price($product->price) }}</div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>


                        <div class="col-12">
                            <h3>{{ __('Latest products') }}</h3>
                            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4">
                                @foreach (cart_products(null, 'latest') as $product)
                                <div class="col mb-4">
                                    <div class="card h-100 card-product-grid">
                                        <div class="card-header card-product-grid-image">
                                            <a href="{{ cart_product_url($product->id) }}">
                                                <img src="@if($product->image){{ thumb($product->image) }} @else {{ asset('assets/img/no-image.png') }} @endif" class="img-fluid"
                                                    alt="{{ $product->title }}">
                                            </a>
                                        </div>
                                        <div class="card-body card-product-grid-body">
                                            <div class="card-product-grid-title">
                                                <a title="{{ $product->title }}" href="{{ cart_product_url($product->id) }}">{{ $product->title }}</a>
                                            </div>
                                            <div class="card-product-grid-price">{{ price($product->price) }}</div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                    </div>

                </div>

            </section>

        </div>

        @include("{$template_view}.footer")

    </div>    

</body>

</html>