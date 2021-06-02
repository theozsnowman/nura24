<!DOCTYPE html>
<html lang="{{ $locale }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ $page->meta_title ?? $page->title}}</title>
    <meta name="description" content="{{ $page->meta_description ?? substr(strip_tags($page->content), 0, 250) }}">

    @include("{$template_view}.global-head")

</head>

<body>

    <div id="all">

        <div id="content-wrap">

            @include("{$template_view}.navigation")

            <section class="bar no-mb">
                <div class="container">
                    <div class="heading text-center">
                        <h1>{{ $page->title }}</h1>
                    </div>

                    @if($page->image)<img class="img-fluid card-img-top mb-4" src="{{ asset('uploads/'.$page->image) }}" alt="{{ $page->title }}" title="{{ $page->title }}">@endif
                    {!! $page->content !!}
                </div>
            </section>

        </div>

        @include("{$template_view}.footer")

    </div>

</body>

</html>