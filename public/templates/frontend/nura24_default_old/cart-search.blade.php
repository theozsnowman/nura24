<!DOCTYPE html>
<html lang="{{ $locale }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ $s }}</title>
    <meta name="description" content="{{ $s }}">

    @include("{$template_view}.global-head")   
</head>

<body>

    <div id="all">

        <div id="content-wrap">

            @include("{$template_view}.navigation")

            @include("{$template_view}.blocks.search-cart")      

            <section class="bar background-white no-mb">

                <div class="container">
                   
                    <div class="row">                                                                   

                        <div class="col-12">

                            <div class="heading">
                                <h2>{{ $s }} - {{ __('search results') }}</h2>
                            </div>

                            <div class="clearfix mb-3"></div>

                            <div class="row row-cols-1 row-cols-sm-3 row-cols-md-3">
                                @foreach($products as $product)
                                <div class="col mb-4">
                                    <div class="card h-100 card-product-grid">                                        
                                        <div class="card-header card-product-grid-image">
                                            <a title="{{ $product->translated_title ?? $product->title }}" href="{{ cart_product_url($product->id) }}">
                                                <img src="@if($product->image){{ thumb($product->image) }} @else {{ asset('assets/img/no-image.png') }} @endif" class="img-fluid"
                                                    alt="{{ $product->translated_title ?? $product->title }}">
                                            </a>
                                        </div>
                                        <div class="card-body card-product-grid-body">                                           

                                            <div class="card-product-grid-title">
                                                <a title="{{ $product->translated_title ?? $product->title }}" href="{{ cart_product_url($product->id) }}">{{ $product->translated_title ?? $product->title }}</a>
                                            </div>

                                            <div class="card-product-grid-price">
                                                {{ price($product->price) }}
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                @endforeach

                                {{ $products->links() }}

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