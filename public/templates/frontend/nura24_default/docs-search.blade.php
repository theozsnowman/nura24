<!DOCTYPE html>
<html lang="{{ $locale }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ $s}} - {{ __('search in documentation') }}</title>
    <meta name="description" content="{{ $s}} - {{ __('search in documentation') }} | {{ lang_meta()->site_short_title }}">

    @include("{$template_view}.global-head")

    <!-- Syntax highlight-->
    <link rel="stylesheet" href="{{ asset("$template_path/assets/css/prism.css") }}">
</head>

<body>

    <div id="all">

        <div id="content-wrap">

            @include("{$template_view}.navigation")

            <section class="bar bg-search no-mb">
                <div class="col-md-4 offset-md-4">
                    <form methpd="get" action="{{ search_docs_url() }}">
                        <input type="text" class="form-control docs-search" name="s" placeholder="{{ __('Search in documentation') }}">
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
                                    <li class="breadcrumb-item">{{ __('Search results') }}</li>
                                </ol>
                            </nav>
                        </div>
                    </div>

                    <div class="row">

                        <div class="col-xl-3 col-sm-12 col-12">
                            <div class="bg-light p-3">
                                @foreach(nura_docs_categ_tree() as $level0)

                                <a class="docs_root" href="{{ docs_url($level0->id) }}">{{ $level0->title}}</a>
                                <div class="mb-1"></div>

                                @foreach($level0->children as $level1)
                                <a class="docs_categ" href="{{ docs_url($level1->id) }}"> {{ $level1->title }}</a>
                                <div class="mb-1"></div>
                                @foreach(nura_docs_categ_tree($level1->id) as $level2)
                                <a class="ml-4 docs_subcateg" href="{{ docs_url($level2->id) }}"> {{ $level2->title }}</a>
                                <div class="mb-1"></div>
                                @foreach(nura_docs_categ_tree($level2->id) as $level3)
                                <a class="ml-5 docs_subcateg" href="{{ docs_url($level3->id) }}"> {{ $level3->title }}</a>
                                <div class="mb-1"></div>
                                @endforeach
                                @endforeach
                                @endforeach
                                <div class="mb-4"></div>

                                @endforeach
                            </div>
                        </div>


                        <div class="col-xl-9 col-sm-12 col-12 pl-5">

                            <h2>{{ $s }} - {{ __('search results') }}</h2>
                            <p class="small textmuted">"{{ $s }}" - {{ $articles->total() ?? 0 }} {{ __('articles') }}</p>

                            @foreach($articles as $article)
                            <h3><a href="{{ docs_url($article->categ_id) }}#{{ $article->slug}}">{{ $article->title }}</a></h3>
                            <span class="small textmuted">{!! substr(strip_tags($article->content), 0, 300) !!}</span>
                            <div class="mb-4"></div>
                            @endforeach

                            {{ $articles->appends(['s' => $s])->links() }}

                        </div>

                    </div>
                </div>
            </section>

        </div>

        @include("{$template_view}.footer")

        <script src="{{ asset($template_path.'/assets/js/prism.js') }}"></script>

    </div>

</body>

</html>