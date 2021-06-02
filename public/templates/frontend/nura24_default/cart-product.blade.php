<!DOCTYPE html>
<html lang="{{ $locale }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ $product->meta_title ?? $product->title}}</title>
    <meta name="description" content="{{ $product->meta_description ?? $product->summary ?? substr(strip_tags($product->content), 0, 250)}}">

    @include("{$template_view}.global-head")

    <!-- BEGIN CSS for this page -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.2.5/jquery.fancybox.min.css" />
    <!-- END CSS for this page -->
</head>

<body>

    <div id="all">

        <div id="content-wrap">

            @include("{$template_view}.navigation")

            <section>

                <div class="container">

                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ homepage() }}">{{ __('Home') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ cart_url() }}">{{ __('Shop') }}</a></li>
                            @foreach(breadcrumb($product->categ_id, 'cart') as $categ)
                            <li class="breadcrumb-item"><a href="{{ cart_url($categ->id) }}">{{ $categ->title }}</a></li>
                            @endforeach
                        </ol>
                    </nav>

                    <div class="cart_product_title">
                        <h1>{{ $product->title }}</h1>
                    </div>


                    <div class="row">
                        <div class="col-xl-4 col-md-6 col-12">
                            @if($product->image)<a data-fancybox="gallery" href="{{ image($product->image) }}"><img class="img-fluid card-img-top mb-4" src="{{ thumb($product->image) }}" alt="{{ $product->title }}"
                                    title="{{ $product->title }}"></a>@endif

                            @if(count($images)>0)
                            <p>{{ __('Images gallery') }}</p>
                            <div class="row">
                                @foreach($images as $image)
                                <div class="col-lg-3 col-md-4 col-6 mb-4">
                                    <a data-fancybox="gallery" href="{{ image($image->file) }}">
                                        <img class="img-fluid mb-2" src="{{ thumb($image->file) }}" alt="{{ $image->description ?? $product->title }} - {{ $image->file }} "
                                            title="{{ $image->description ?? $product->title }}">
                                    </a>
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>

                        <div class="col-xl-8 col-md-6 col-12">

                            <div class="cart_product_summary mb-4">
                                {{ __('Product code: ')}} {{ strtoupper($product->sku) }}

                            </div>

                            <div class="product_price mb-2">
                                {{ price($product->price) }}
                            </div>

                            @if($product->disable_orders==1)
                            <div class="alert alert-danger">
                                {{ __('Orders are disabled for this product.') }}
                                @if($product->disable_orders_notes)
                                <div class="alert alert-warning">
                                    {!! nl2br($product->disable_orders_notes) !!}
                                </div>
                                @endif
                            </div>
                            @else
                            <form class="d-flex justify-content-left" action="{{ route('cart.add', ['lang' => $lang, 'id' => $product->id]) }}" method="post">
                                @csrf
                                <button class="btn btn-primary btn-md my-0 p" type="submit">{{ __('Add to cart') }}
                                    <i class="fas fa-shopping-cart ml-1"></i>
                                </button>
                            </form>
                            @endif
                        </div>

                    </div>

                    {!! $product->content !!}

                    @if($product->help_info)
                    <div class="alert alert-info mt-3 mb-3">
                        {!! $product->help_info !!}
                    </div>
                    @endif

                    @if(count($related_products)>0)
                    <h3 class="mt-5 mb-3">{{ __('Related products') }}</h3>
                    <div class="row">
                        @foreach ($related_products as $product)
                        <div class="col mb-4">
                            <div class="card h-100 card-product-grid">                                        
                                <div class="card-header card-product-grid-image">
                                    <a title="{{ $product->title }}" href="{{ cart_product_url($product->id) }}">
                                        <img src="@if($product->image){{ thumb($product->image) }} @else {{ asset('assets/img/no-image.png') }} @endif" class="img-fluid"
                                            alt="{{ $product->title }}">
                                    </a>
                                </div>
                                <div class="card-body card-product-grid-body">                                           

                                    <div class="card-product-grid-title">
                                        <a title="{{ $product->title }}" href="{{ cart_product_url($product->id) }}">{{ $product->title }}</a>
                                    </div>

                                    <div class="card-product-grid-price">
                                        {{ price($product->price) }}
                                    </div>

                                </div>
                            </div>
                        </div>                       
                        @endforeach
                    </div>
                    @endif

            </section>

        </div>

        @include("{$template_view}.footer")

    </div>


    <!-- BEGIN Java Script for this page -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.2.5/jquery.fancybox.min.js"></script>

    @if ($message = Session::get('success') and $message=='added_to_cart'))
    <script type="text/javascript">
        $(window).on('load',function(){
            $('#added-to-cart').modal('show');
        });
    </script>
    @include("{$template_view}.blocks.added-to-cart")
    @endif

</body>

</html>