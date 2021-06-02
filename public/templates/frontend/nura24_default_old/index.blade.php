<!doctype html>
<html lang="{{ $locale }}">

<head>
    <title>{{ $homepage_meta_title }}</title>
    <meta name="description" content="{{ $homepage_meta_description }}">

    @include("{$template_view}.global-head")
</head>

<body>

    <div id="all">

        <div id="content-wrap">

            @include("{$template_view}.navigation")

            @if(check_module('slider'))
            @include("{$template_view}.slider")
            @endif   

            <section class="bar background-white no-mb">

                <div class="container">

                    <div class="float-right">
                        <a class="btn btn-light btn-sm" href="{{ posts_url() }}" title="{{ __('View all posts') }}">{{ __('View all posts') }}</a>
                    </div>

                    <h3>{{ __('Latest posts') }}</h3>

                    <div class="row">
                        @foreach (posts() as $post)
                        <div class="col-lg-3 col-md-4 col-12">
                            <div class="box-post mb-4">
                                @if($post->image)
                                <a title="{{ $post->title }}" href="{{ post_url($post->id) }}">
                                    <img src="{{ thumb($post->image) }}" alt="{{ $post->title }}" class="img-fluid" style="height: 22vh; width: 100%; object-fit: cover;"></a>
                                @endif
                                <div class="info">
                                    {{ date_locale($post->created_at, 'datetime') }} / <a title="{{ $post->categ_title }}" href="{{ posts_url($post->categ_id) }}">{{ $post->categ_title }}</a>
                                </div>
                                <a class="title" title="{{ $post->title }}" href="{{ post_url($post->id) }}">{{ $post->title }}</a>
                            </div>
                        </div>
                        @endforeach
                    </div>

                </div>

            </section>

        </div>

        @include("{$template_view}.footer")

    </div>

</body>

</html>