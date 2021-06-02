<!DOCTYPE html>
<html lang="{{ $locale }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ $categ->meta_title}} | {{ site()->short_title }}</title>
    <meta name="description" content="{{ $categ->meta_description}}">

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
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ homepage() }}">{{ __('Home') }}</a></li>
                                    <li class="breadcrumb-item"><a href="{{ cart_url() }}">{{ __('Shop') }}</a></li>
                                    @foreach(breadcrumb($categ->id, 'cart') as $nav_categ)
                                    <li class="breadcrumb-item"><a href="{{ cart_url($nav_categ->id) }}">{{ $nav_categ->title }}</a></li>
                                    @endforeach
                                </ol>
                            </nav>
                        </div>
                    </div>

                    <div class="row">                        
                        
                        <div class="col-xl-3 col-sm-12 col-12">
                            <div class="bg-light p-3">
                                @foreach(cart_categ_tree() as $level0)

                                <a class="docs_root @if($categ->id==$level0->id) text-danger @endif" href="{{ cart_url($level0->id) }}">{{ $level0->title}}</a>
                                <div class="mb-1"></div>

                                @if(in_array($categ->id, $level0->tree_ids))
                                @foreach($level0->children as $level1)
                                <a class="docs_categ @if($categ->id==$level1->id) text-danger bold @endif" href="{{ cart_url($level1->id) }}"> {{ $level1->title }}</a>
                                <div class="mb-1"></div>
                                @foreach(cart_categ_tree($level1->id) as $level2)
                                <a class="ml-4 docs_subcateg @if($categ->id==$level2->id) text-danger bold @endif" href="{{ cart_url($level2->id) }}"> {{ $level2->title }}</a>
                                <div class="mb-1"></div>
                                @foreach(cart_categ_tree($level2->id) as $level3)
                                <a class="ml-5 docs_subcateg @if($categ->id==$level3->id) text-danger bold @endif" href="{{ cart_url($level3->id) }}"> {{ $level3->title }}</a>
                                <div class="mb-1"></div>
                                @endforeach
                                @endforeach
                                @endforeach
                                @endif
                                <div class="mb-4"></div>

                                @endforeach
                            </div>
                        </div>


                        <div class="col-xl-9 col-md-8 col-12 pl-5">

                            <h3 class="mt-3">{{ $categ->title }} <span class="text-muted text-small">({{ $products->total() ?? 0 }} {{ __('products') }})</span></h3>

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