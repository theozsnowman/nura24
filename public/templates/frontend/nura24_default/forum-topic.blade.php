<!doctype html>
<html lang="{{ $locale }}">

<head>
    <title>{{ $topic->title }} - {{ __('Forum') }} {{ lang_meta()->site_short_title }}</title>
    <meta name="description" content="{{ substr(strip_tags($topic->content), 0, 300) }}">

    @include("{$template_view}.global-head")

    <!-- Text editor-->
    <script src="{{ asset("$template_path/assets/vendor/trumbowyg/trumbowyg.min.js") }}"></script>
    <script src="{{ asset("$template_path/assets/vendor/prism/prism.js") }}"></script>
    <script src="{{ asset("$template_path/assets/vendor/trumbowyg/plugins/highlight/trumbowyg.highlight.min.js") }}"></script>
    <script src="{{ asset("$template_path/assets/vendor/trumbowyg/plugins/noembed/trumbowyg.noembed.min.js") }}"></script>
    <link rel="stylesheet" href="{{ asset("$template_path/assets/vendor/prism/prism.css") }}">
    <link rel="stylesheet" href="{{ asset("$template_path/assets/vendor/trumbowyg/plugins/highlight/ui/trumbowyg.highlight.min.css") }}">
    <link rel="stylesheet" href="{{ asset("$template_path/assets/vendor/trumbowyg/ui/trumbowyg.min.css") }}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.2.5/jquery.fancybox.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.min.js"></script>
</head>

<body>

    <div id="all">

        <div id="content-wrap">

            @include("{$template_view}.navigation")

            <section class="bar background-white no-mb">
                <div class="container">
                    <div class="row">

                        <div class="col-12">

                            @if ($message = Session::get('error'))
                            <div class="alert alert-danger">
                                @if ($message=='error_content') {{ __('Error. Please input content') }} @endif
                                @if ($message=='error_topic_not_active') {{ __("Error. You can't reply to this topic") }} @endif
                            </div>
                            @endif

                            @if ($message = Session::get('success'))
                            <div class="alert alert-info font-weight-bold">
                                @if ($message=='reported')<i class="fas fa-exclamation-triangle"></i> {{ __('Report was sent. Thank you') }} @endif
                                @if ($message=='post_created'){{ __('Your reply was added') }} @endif
                            </div>
                            @endif

                            <div class="row">
                                <div class="col-12 mb-3">
                                    <span class="float-right">
                                        <a class="btn btn-forum ml-4" href="{{ route('forum.topic.create') }}"><i class="fas fa-pen" aria-hidden="true"></i> {{ __('New topic') }}</a>
                                    </span>

                                    <span class="float-right">
                                        <form class="form-inline">
                                            <input class="form-control" name="search" placeholder="{{ __('Search in forum') }}">
                                        </form>
                                    </span>
                                </div>

                                <div class="col-12 mb-4">

                                    <nav aria-label="breadcrumb">
                                        <ol class="breadcrumb">
                                            <li class="breadcrumb-item"><a href="{{ homepage_url() }}">{{ __('Home') }}</a></li>
                                            <li class="breadcrumb-item"><a href="{{ forum_url() }}">{{ __('Forum') }}</a></li>
                                            @foreach(breadcrumb($categ->id, 'forum') as $b_categ)
                                            <li class="breadcrumb-item"><a href="{{ route('forum.categ', ['slug' => $b_categ->slug]) }}">{{ $b_categ->title }}</a></li>
                                            @endforeach
                                        </ol>
                                    </nav>

                                    <h3>{{ $topic->title }}</h3>
                                    <div class="text-muted small mb-3">{{ __('Created at') }} {{ date_locale($topic->created_at, 'datetime') }} {{ __('by') }} {{ $topic->author_name }}</div>

                                    <div class="card card-forum">
                                        @include("{$template_view}.blocks.forum-topic-header")

                                        @include("{$template_view}.blocks.forum-topic-body")
                                    </div>
                                </div>

                                <div class="col-12 mb-4">
                                    @foreach($posts as $post)
                                    <div class="card card-forum mb-4">

                                        @include("{$template_view}.blocks.forum-post-header")

                                        <div class="card-body @if($categ->type=='question' and $post->count_best_answer>0 and $loop->index==0) post-best-answer @endif">
                                            @include("{$template_view}.blocks.forum-post-body")
                                        </div>
                                    </div>
                                    @endforeach

                                    {{ $posts->links() }}
                                </div>

                                <div class="col-12 mb-4">
                                    @if (!Auth::user())
                                    {{ __('You must be logged to post new topic')}}. <a href="{{ route('login') }}">{{ __('Login') }}</a> {{ __('or') }} <a href="{{ route('register') }}">{{ __('register account') }}</a>
                                    @else

                                    <a name="reply"></a>

                                    @if($topic->status!='active')
                                    <div class="text-danger font-weight-bold">{{ __('This topic is closed')}}</div>
                                    @else
                                    <form method="post" enctype="multipart/form-data">
                                        @csrf

                                        <div class="form-group">
                                            <label>{{ __('Post reply') }}</label>
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
                                    <button type="submit" class="btn btn-dark"><i class="fas fa-share"></i> {{ __('Post reply') }}</button>
                                </div>

                                </form>
                                @endif
                                @endif
                            </div>

                        </div>

                    </div>

                </div>
        </div>
        </section>

    </div>
    
    @include("{$template_view}.footer")

    <script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.2.5/jquery.fancybox.min.js"></script>

    <script>
        $(document).ready(function() {
                'use strict';

                bsCustomFileInput.init();     

                $('.editor').trumbowyg({
                    btns: [
                        ['formatting', 'strong', 'em', 'highlight'],
                        ['link', 'noembed'],
                        ['unorderedList', 'orderedList', 'horizontalRule', 'removeformat'],
                    ]
                });	        

                $(".like").click(function(post_id) {
                    alert( "Handler for called." );
                    
                    $.ajax({
                        type: 'GET',
                        data: {post_id: post_id},
                        url: '/forum-like-post?id=4',
                        success: function(data) {
                            if(data=='liked') {
                                var elem = document.getElementById('like_success');
                                $(elem).show();
                            }
                            if(data=='already_liked') {
                                var elem = document.getElementById('like_error');
                                var elem2 = document.getElementById('like_success');
                                $(elem2).hide();
                                $(elem).show();
                            }
                            if(data=='login_required') {
                                var elem = document.getElementById('login_required');
                                $(elem).show();
                            }
                        }
                        });
                });
        
            });
    </script>
    </div>

</body>

</html>