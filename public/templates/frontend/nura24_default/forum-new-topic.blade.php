<!doctype html>
<html lang="{{$locale }}">

<head>
    <title>{{ __('Create forum topic') }}</title>
    <meta name="description" content="{{ __('Create forum topic') }}">

    @include("{$template_view}.global.head")

    <!-- Text editor-->
    <script src="{{ asset("$template_path/assets/vendor/trumbowyg/trumbowyg.min.js") }}"></script>
    <script src="{{ asset("$template_path/assets/vendor/prism/prism.js") }}"></script>
    <script src="{{ asset("$template_path/assets/vendor/trumbowyg/plugins/highlight/trumbowyg.highlight.min.js") }}"></script>
    <script src="{{ asset("$template_path/assets/vendor/trumbowyg/plugins/noembed/trumbowyg.noembed.min.js") }}"></script>
    <link rel="stylesheet" href="{{ asset("$template_path/assets/vendor/prism/prism.css") }}">
    <link rel="stylesheet" href="{{ asset("$template_path/assets/vendor/trumbowyg/plugins/highlight/ui/trumbowyg.highlight.min.css") }}">
    <link rel="stylesheet" href="{{ asset("$template_path/assets/vendor/trumbowyg/ui/trumbowyg.min.css") }}">

    <script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.min.js"></script>
</head>

<body>

    <div id="all">

        <div id="content-wrap">

            @include("{$template_view}.global.navigation")

            <section>

                <div class="container">

                    <div class="row">

                        <div class="col-12">

                            <div class="heading">
                                <h2>{{ __('Create forum topic') }}</h2>
                            </div>

                            @if ($message = Session::get('error'))
                            <div class="alert alert-danger">
                                @if ($message=='error_categ') {{ __('Error. Select category') }} @endif
                                @if ($message=='error_title') {{ __('Error. Input title') }} @endif
                                @if ($message=='error_content') {{ __('Error. Please input content') }} @endif
                            </div>
                            @endif

                            @if (!Auth::user())
                            {{ __('You must be logged to post new topic')}}. <a href="{{ route('login') }}">{{ __('Login') }}</a> {{ __('or') }} <a href="{{ route('register') }}">{{ __('register account') }}</a>
                            @else

                            <form method="post" action="{{ route('forum.topic.store') }}" enctype="multipart/form-data">
                                @csrf

                                <div class="form-group">
                                    <label>{{ __('Select forum') }}</label><br>
                                    <select name="categ_id" class="form-control custom-select custom-select-lg col-md-6 col-12" required>
                                        <option value="">- {{ __('select') }} -</option>
                                        @foreach ($categories as $categ)
                                        @include("{$template_view}.loops.forum-categories-select-loop", $categ)
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>{{ __('Title (subject)') }}</label>
                                    <input class="form-control form-control-lg" name="title" type="text" required>
                                </div>

                                <div class="form-group">
                                    <label>{{ __('Content') }}</label>
                                    <textarea class="form-control editor" name="content" required></textarea>
                                </div>

                                @if(($config->forum_upload_images_enabled ?? null) =='yes')
                                <div class="form-group">
                                    <label>{{ __('Attach images') }} ({{ __('maximum 6 images') }})</label>
                                    <small class="form-text text-muted mb-3">{{ __('Maximum 6 images. File extensions: jpg,jpeg,bmp,png,gif,webp') }}</small>

                                    <div class="row">
                                        @for($i=1; $i<=6; $i++) <div class="col-12 col-md-6 mb-3">
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="validatedCustomFile_{{ $i }}" name="image_{{ $i }}">
                                                <label class="custom-file-label" for="validatedCustomFile_{{ $i }}">{{ __('Choose file') }}...</label>
                                            </div>
                                    </div>
                                    @endfor
                                </div>

                        </div>
                        @endif

                        <div class="form-group">
                            <button type="submit" class="btn btn-forum"><i class="fas fa-share"></i> {{ __('Create forum topic') }}</button>
                        </div>

                        </form>
                        @endif

                    </div>

                </div>

            </section>

        </div>

        @include("{$template_view}.global.footer")

    </div>

    <script>
        $(document).ready(function() {
            'use strict';

            bsCustomFileInput.init();     

            $('.editor').trumbowyg({
                btns: [
                    ['p', 'blockquote', 'strong', 'em', 'highlight'],
                    ['link', 'noembed'],
                    ['unorderedList', 'orderedList', 'horizontalRule', 'removeformat'],
                ]
            });	        
        });
    </script>

</body>

</html>