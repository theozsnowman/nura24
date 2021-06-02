<!DOCTYPE html>
<html lang="{{ $locale }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ __('Downloads') }} - {{ lang_meta()->site_short_title }}</title>
    <meta name="description" content="{{ __('All downloads') }} - {{ lang_meta()->site_short_title }}">

    @include("{$template_view}.global-head")
</head>

<body>

    <div id="all">

        <div id="content-wrap">

            @include("{$template_view}.navigation")

            <section class="bar background-white no-mb">
                <div class="container">
                    <div class="row">

                        <div class="col-12">

                            <div class="heading">
                                <h2>{{ __('Downloads') }}</h2>
                            </div>

                            @foreach ($downloads as $download)
                            <h3><a title="{{ __('Download') }} {{ $download->translated_title ?? $download->title }}" href="{{ download_url($download->id) }}">{{ $download->translated_title ?? $download->title }}</a></h3>
                            @if($download->summary)<div class="small text-muted">{{ $download->translated_summary ?? $download->summary }}</div>@endif
                            <div class="mb-3"></div>
                            @endforeach

                            {{ $downloads->links() }}
                        </div>

                    </div>
                </div>
            </section>

        </div>

        @include("{$template_view}.footer")

    </div>

</body>

</html>