<!DOCTYPE html>
<html lang="{{ $locale }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ $product->meta_title }}</title>
    <meta name="description" content="{{ $product->meta_description }}">

    @include("{$template_view}.global-head")
</head>

<body>

    <div id="all">

        <div id="content-wrap">

            @include("{$template_view}.navigation")

            <section class="bar no-mb mt-2">
                <div class="container">

                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ homepage_url() }}">{{ __('Home') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ cart_url() }}">{{ __('Shop') }}</a></li>
                            @foreach(breadcrumb($product->categ_id, 'cart') as $categ)
                            <li class="breadcrumb-item"><a href="{{ cart_url($categ->id) }}">{{ $categ->title }}</a></li>
                            @endforeach
                        </ol>
                    </nav>

                    <div class="cart_product_title">
                        <h1>{{ $product->title }}</h1>
                    </div>

                    {!! $product->content !!}

                    @if($product->help_info)
                    <div class="alert alert-info mt-3 mb-3">
                        {!! $product->help_info !!}
                    </div>
                    @endif

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
                    <form class="d-flex justify-content-left" action="{{ route('cart.add', ['id' => $product->id]) }}" method="post">
                        @csrf
                        <button class="btn btn-primary btn-md my-0 p" type="submit">{{ __('Add to cart') }}
                            <i class="fas fa-shopping-cart ml-1"></i>
                        </button>
                    </form>
                    @endif

                    @if(count($related_products)>0)
                    <h3 class="mt-5 mb-3">{{ __('Related products') }}</h3>
                    @foreach ($related_products as $product)
                    <h5><a class="title" title="{{ $product->title }}" href="{{ cart_product_url($product->id) }}">{{ $product->title }}</a></h5>
                    @endforeach
                    @endif

            </section>

        </div>

        @include("{$template_view}.footer")

    </div>

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