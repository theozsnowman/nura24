<!DOCTYPE html>
<html lang="{{ $locale }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ __('Documentation') }}</title>
    <meta name="description" content="Documentation">

    @include("{$template_view}.global.head")

    <!-- Syntax highlight-->
    <link rel="stylesheet" href="{{ asset($template_path.'/assets/css/prism.css') }}">
</head>

<body>

    <div id="all">

        <div id="content-wrap">

            @include("{$template_view}.global.navigation")

            <section>

                <div class="col-md-4 offset-md-4">

                    <form methpd="get" action="{{ search_docs_url() }}">
                        <input type="text" class="form-control docs-search" name="s" required placeholder="{{ __('Search in documentation') }}">
                    </form>

                </div>

            </section>


            <section>

                <div class="container">
                    
                    <div class="row">

                        <div class="col-12 mb-4">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ homepage() }}">{{ __('Home') }}</a></li>
                                    <li class="breadcrumb-item"><a href="{{ docs_url() }}">{{ __('Documentation') }}</a></li>
                                </ol>
                            </nav>
                        </div>

                        @foreach(docs_categ_tree() as $root_categ)
                        <div class="col-md-4 col-12">

                            @if($root_categ->icon) {!! $root_categ->icon !!}@endif <a class="docs_root" title="{{ $root_categ->title }}" href="{{ docs_url($root_categ->id) }}">{{ $root_categ->title}}</a>
                            <div class="mb-2"></div>

                            @foreach($root_categ->children as $subcateg)
                            <a class="docs_categ" title="{{ $subcateg->title }}" href="{{ docs_url($subcateg->id) }}"> {{ $subcateg->title }}</a>
                            <div class="mb-2"></div>
                            @endforeach
                            <div class="mb-4"></div>
                        </div>
                        @endforeach

                        @if($featured_articles->total() > 0)
                        <div class="container">
                            <hr>
                            <h3>{{ __('Featured articles') }}</h3>
                            <div class="row">
                                @foreach($featured_articles as $article)
                                <div class="col-md-6 col-12">
                                    <h4><i class="fas fa-caret-right"></i> <a title="{{ $article->title }}" href="{{ docs_url($article->categ_id) }}#{{ $article->slug}}">{{ $article->title }}</a></h4>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                    </div>
                </div>

            </section>

        </div>

        @include("{$template_view}.global.footer")

    </div>

    <script src="{{ asset($template_path.'/assets/js/prism.js') }}"></script>

</body>

</html>