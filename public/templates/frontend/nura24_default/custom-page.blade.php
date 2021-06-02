<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ $page->meta_title }}</title>
    <meta name="description" content="{{ $page->meta_description }}">

    @include("{$template_view}.global-head")

</head>

<body>

    <div id="all">

        <div id="content-wrap">

            @include("{$template_view}.navigation")

            <section class="bar no-mb">
                <div class="container">
                    <div class="col-md-12">
                        <div class="heading">
                            <h1>{{ $page->title }}</h1>
                        </div>

                        @if($page->image)<img class="img-fluid card-img-top" src="{{ image($page->image) }}" alt="{{ $page->title }}" title="{{ $page->title }}">@endif
                        <p class="lead">{!! $page->content !!}</p>

                    </div>
                </div>
            </section>

        </div>

        @include("{$template_view}.footer")

    </div>

</body>

</html>