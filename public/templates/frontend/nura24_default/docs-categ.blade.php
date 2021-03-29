<!DOCTYPE html>
<html lang="{{ $locale }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ __('Documentation') }}: {{ $categ->title}} | {{ lang_meta()->site_short_title }}</title>
    <meta name="description" content="{{ __('Documentation for') }} {{ $categ->title}}">

    @include("{$template_view}.global-head")

    <!-- BEGIN CSS for this page -->
    <!-- Syntax highlight-->
    <link rel="stylesheet" href="{{ asset("$template_path/assets/css/prism.css") }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.2.5/jquery.fancybox.min.css" />
    <!-- END CSS for this page -->
</head>

<body>

    <div id="all">

        <div id="content-wrap">

            @include("{$template_view}.navigation")

            <section class="bar bg-search no-mb">
                <div class="col-md-4 offset-md-4">
                    <form methpd="get" action="{{ search_docs_url() }}">
                        <input type="text" class="form-control docs-search" name="s" required placeholder="{{ __('Search in documentation') }}">
                    </form>
                </div>
            </section>

            <section class="bar background-white no-mb">
                <div class="container">

                    <div class="row">
                        <div class="col-12">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ homepage_url() }}">{{ __('Home') }}</a></li>
                                    <li class="breadcrumb-item"><a href="{{ docs_url() }}">{{ __('Documentation') }}</a></li>
                                    @foreach(breadcrumb($categ->id, 'docs') as $categ)
                                    <li class="breadcrumb-item"><a href="{{ docs_url($categ->id) }}">{{ $categ->title }}</a></li>
                                    @endforeach
                                </ol>
                            </nav>
                        </div>
                    </div>

                    <div class="row">

                        <div class="col-xl-3 col-sm-12 col-12">
                            <div class="bg-light p-3">
                                @foreach(docs_categ_tree() as $level0)

                                <a class="docs_root @if($categ->id==$level0->id) text-danger @endif" href="{{ docs_url($level0->id) }}">{{ $level0->title}}</a>
                                <div class="mb-1"></div>

                                @if(in_array($categ->id, $level0->tree_ids))
                                @foreach($level0->children as $level1)
                                <a class="docs_categ @if($categ->id==$level1->id) text-danger bold @endif" href="{{ docs_url($level1->id) }}"> {{ $level1->title }}</a>
                                <div class="mb-1"></div>
                                @foreach(docs_categ_tree($level1->id) as $level2)
                                <a class="ml-4 docs_subcateg @if($categ->id==$level2->id) text-danger bold @endif" href="{{ docs_url($level2->id) }}"> {{ $level2->title }}</a>
                                <div class="mb-1"></div>
                                @foreach(docs_categ_tree($level2->id) as $level3)
                                <a class="ml-5 docs_subcateg @if($categ->id==$level3->id) text-danger bold @endif" href="{{ docs_url($level3->id) }}"> {{ $level3->title }}</a>
                                <div class="mb-1"></div>
                                @endforeach
                                @endforeach
                                @endforeach
                                @endif
                                <div class="mb-4"></div>

                                @endforeach
                            </div>
                        </div>


                        <div class="col-xl-9 col-sm-12 col-12 pl-5">

                            <h1>{{ $categ->title }}</h1>

                            @foreach(docs_categ_tree($categ->id) as $subcateg)
                            <h4 class="bold"><a href="{{ docs_url($subcateg->id) }}">{{ $subcateg->title}}</a></h4>
                            <div class="mb-4"></div>
                            @endforeach

                            @foreach($categ_articles as $article)
                            <h5 class="bold"><a href="{{ docs_url($categ->id) }}#{{ $article->slug }}"># {{ $article->title }}<a></h5>
                            @endforeach

                            <hr>

                            @foreach($categ_articles as $article)
                            <a name="{{ $article->slug }}" class="anchor"></a>
                            <h2 class="mt-4"><span class="text-danger bold">#</span> {{ $article->title }}</h2>
                            {!! $article->content !!}
                            @endforeach

                        </div>

                    </div>
                </div>
            </section>

        </div>

        @include("{$template_view}.footer")

        <script src="{{ asset($template_path.'/assets/js/prism.js') }}"></script>

        <!-- BEGIN Java Script for this page -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.2.5/jquery.fancybox.min.js"></script>
        <!-- END Java Script for this page -->

    </div>

</body>

</html>