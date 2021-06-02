<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Frequently asked questions</title>
    <meta name="description" content="Frequently asked questions">

    @include("{$template_view}.global-head")

    <!-- Syntax highlight-->
    <link rel="stylesheet" href="{{ asset($template_path.'/assets/css/prism.css') }}">
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
                                <h3>{{ __('Frequently asked questions') }}</h3>
                            </div>

                            <div class="accordion mb-4" id="accordionFAQ">
                                @foreach($faqs as $faq)
                                <div class="card">
                                    <div class="card-header" id="heading_{{ $loop->index }}">
                                        <h2 class="mb-0">
                                            <button class="btn btn-link @if($loop->index>0) collapsed @endif" type="button" data-toggle="collapse" data-target="#collapse_{{ $loop->index }}" aria-expanded="false"
                                                aria-controls="collapse_{{ $loop->index }}">
                                                <b>{{ $faq->title }}</b>
                                            </button>
                                        </h2>
                                    </div>

                                    <div id="collapse_{{ $loop->index }}" class="collapse" aria-labelledby="heading_{{ $loop->index }}" data-parent="#accordionFAQ">
                                        <div class="card-body">
                                            {!! $faq->content !!}
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

        <script src="{{ asset($template_path.'/assets/js/prism.js') }}"></script>

    </div>

</body>

</html>