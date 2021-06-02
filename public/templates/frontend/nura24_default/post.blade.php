<!DOCTYPE html>
<html lang="{{ $locale }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ $post->meta_title ?? $post->title}}</title>
    <meta name="description" content="{{ $post->meta_description ?? $post->summary ?? strip_tags(substr($post->content, 0, 300)) }}">

    @include("{$template_view}.global.head")

    <meta property="og:title" content="{{ $post->title }}" />
    @if($post->image)<meta property="og:image" content="{{ image($post->image) }}" />@endif
    <meta property="og:site_name" content="{{ lang_meta()->site_short_title }}" />
    <meta property="og:description" content="{{ $post->meta_description ?? $post->summary ?? strip_tags(substr($post->content, 0, 300)) }}" />
    <meta property="fb:app_id" content="{{ $config->facebook_app_id ?? null }}" />
    <meta property="og:type" content="article" />

    <!-- BEGIN CSS for this page -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.2.5/jquery.fancybox.min.css" />
    <!-- END CSS for this page -->

    @if(($config->posts_comments_antispam_enabled ?? null) == 1)
    <script src="https://www.google.com/recaptcha/api.js?render={{ $config->google_recaptcha_site_key }}"></script>
    <script>
        grecaptcha.ready(function () {
                grecaptcha.execute('{{ $config->google_recaptcha_site_key }}', { action: 'contact' }).then(function (token) {
                    var recaptchaResponse = document.getElementById('recaptchaResponse');
                    recaptchaResponse.value = token;
                });
            });
    </script>
    @endif

    <!-- ShareThis -->
    <script type='text/javascript' src='https://platform-api.sharethis.com/js/sharethis.js#property=5fc8239c63a0a50014594325&product=sop' async='async'></script>
</head>

<body>

    <div id="all">

        <div id="content-wrap">

            @include("{$template_view}.global.navigation")

            @include("{$template_view}.blocks.search-posts")             
            
            <section>

                <div class="container">

                    <div class="row">

                        <div class="col-12">

                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ homepage() }}">{{ __('Home') }}</a></li>
                                    <li class="breadcrumb-item"><a href="{{ posts_url() }}">{{ __('Blog') }}</a></li>
                                    @foreach(breadcrumb($post->categ_id) as $categ)
                                    <li class="breadcrumb-item"><a href="{{ posts_url($categ->id) }}">{{ $categ->title }}</a></li>
                                    @endforeach
                                </ol>
                            </nav>

                            <div class="text-center">

                                <h1>{{ $post->title }}</h1>

                                <p class="text-muted text-small">
                                    @if($post->created_at)<i class="far fa-clock"></i> {{ date_locale($post->created_at) }} @endif
                                    <i class="far fa-user-circle ml-2"></i> <a href="{{ profile_url($post->user_id) }}">{{ $post->author_name }}</a>
                                    @if($post->hits)<i class="far fa-eye ml-2"></i> {{ $post->hits }} {{ __('visits') }} @endif
                                    @if($comments->total() > 0)<i class="far fa-comments ml-2"></i> <a href="#comments">{{ $comments->total() }} {{ __('comments') }}</a> @endif
                                    @if($post->minutes_to_read > 0)<i class="far fa-clock ml-2"></i> {{ $post->minutes_to_read }} {{ __('min. to read') }} @endif
                                </p>

                            </div>

                            @if($post->image)<img class="img-fluid card-img-top mb-4" src="{{ image($post->image) }}" alt="{{ $post->title }}" title="{{ $post->title }}">@endif

                            @if($post->summary)<div class="font-weight-bold mb-4">{{ $post->summary }}</div>@endif

                            {!! $post->content !!}
                            
                            @foreach($tags as $tag)
                            <a class="post-tag" title="{{ $tag->tag }}" href="{{ posts_tag_url($tag->slug) }}">{{ $tag->tag }}</a>
                            @endforeach

                            @if($images)
                            <div class="row">
                                @foreach($images as $image)
                                <div class="col-md-3 col-12 text-center">
                                    <a data-fancybox="gallery" href="{{ image($image->file) }}">
                                        <img class="img-fluid mb-4" src="{{ thumb($image->file) }}" alt="{{ $post->title }} - {{ $image->id }} " title="{{ $post->title }}">
                                    </a>
                                </div>
                                @endforeach
                            </div>
                            @endif

                            <hr>

                            <div class="row">
                                <div class="col-12">
                                    @if($likes_enabled)
                                    <div class="float-left">
                                        <button class="btn btn-light like"><i class="far fa-thumbs-up fa-1x"></i> {{ $post->likes }} {{ __('likes') }}</button>
                                        <div id="like_success" class="text-success small mt-2" style="display: none; font-weight:bold">{{ __('You like this') }}</div>
                                        <div id="like_error" class="text-danger small mt-2" style="display: none; font-weight:bold">{{ __('You already like this') }}</div>
                                        <div id="login_required" class="text-danger small mt-2" style="display: none; font-weight:bold">{{ __('You must login to like') }}: <a
                                                href="{{ route('login') }}">{{ __('Login') }}</a></div>
                                    </div>
                                    @endif

                                    <div class="float-right">
                                        <div class="sharethis-inline-share-buttons"></div>
                                    </div>

                                </div>
                            </div>

                            @if($comments->total()>0)
                            <p class="mt-4 mb-2">{{ $comments->total() }} {{ __('comments') }}</p>

                            <a class="anchor" name="comments" id="comments"></a>

                            @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif

                            @if ($message = Session::get('success'))
                            <div class="alert alert-success">
                                @if ($message=='comment_added') {{ __('Comment added') }} @endif
                            </div>
                            @endif

                            @if ($message = Session::get('error'))
                            <div class="alert alert-danger">
                                @if ($message=='login_required') {{ __('You must be logged to comment') }} @endif
                                @if ($message=='recaptcha_error') {{ __('Wrong antispam') }} @endif
                            </div>
                            @endif

                            <ul class="comment-list">
                                @foreach ($comments as $comment)
                                <li class="comment p-2 bg-light mb-3">
                                    <div class="comment-body">
                                        @if($comment->user_id)
                                        @if($comment->author_avatar)
                                        <img src="{{ thumb($comment->author_avatar) }}" alt="{{ $comment->author_name }}" class="img-fluid rounded-circle" style="max-height: 35px;">
                                        @endif
                                        <span class="author"><a href="{{ profile_url($comment->user_id) }}">{{ $comment->author_name }}</a></span>
                                        <span class="meta">{{ date_locale($comment->created_at, 'datetime') }}</span>
                                        @else
                                        <span class="author">{{ $comment->name }}</span>
                                        <span class="meta">{{ date_locale($comment->created_at, 'datetime') }}</span>
                                        @endif
                                        <div class="comment">{!! nl2br(e($comment->comment)) !!}</div>
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                            <div class="clearfix"></div>
                            {{ $comments->fragment('comments')->links() }}
                            @endif

                            @if(($config->posts_comments_disabled ?? null) == 0 && $post->disable_comments != 1)
                            @if(($config->posts_comments_require_login ?? null) == 1 && !Auth::check())
                            {{ __('You must login to comment') }}: <a href="{{ route('login') }}">{{ __('Login') }}</a>
                            @else
                            <div class="comment-form-wrap pt-2">
                                <p class="mt-4 mb-2">{{ __('Leave a comment') }}</p>
                                <form class="p-3 bg-light" method="post" action="{{ posts_submit_comment_url($post->categ_slug, $post->slug) }}">
                                    @csrf
                                    <div class="form-group">
                                        <label for="name">{{ __('Name') }}</label>
                                        <input type="text" class="form-control" name="name" required @if(Auth::user()) value="{{ Auth::user()->name }}" readonly @endif>
                                    </div>
                                    <div class="form-group">
                                        <label for="email">{{ __('Email') }}</label>
                                        <input type="email" class="form-control" name="email" required @if(Auth::user()) value="{{ Auth::user()->email }}" readonly @endif>
                                    </div>
                                    <div class="form-group">
                                        <label for="message">{{ __('Message') }}</label>
                                        <textarea name="comment" rows="6" class="form-control" required></textarea>
                                    </div>
                                    <div class="form-group">
                                        <input type="hidden" name="id" value="{{ $post->id }}">
                                        <input type="hidden" name="recaptcha_response" id="recaptchaResponse">
                                        <input type="submit" value="{{ __('Post comment') }}" class="btn btn-primary">
                                    </div>

                                </form>
                            </div>
                            @endif
                            @endif


                            <h3 class="mt-5 mb-3">{{ __('Related posts') }}</h3>

                            <div class="row">
                                @foreach (posts() as $related)
                                <div class="col-lg-4 col-md-6">
                                    <div class="box-post">
                                        @if($related->image)
                                        <div class="image">
                                            <a title="{{ $related->title }}" href="{{ post_url($related->id) }}">
                                                <img src="{{ thumb($related->image) }}" alt="{{ $related->title }}" class="img-fluid darker"></a>
                                        </div>
                                        @endif
                                        <div class="info">
                                            {{ date_locale($related->created_at) }}
                                        </div>
                                        <a class="title" title="{{ $related->title }}" href="{{ post_url($related->id) }}">{{ $related->title }}</a>
                                    </div>
                                </div>
                                @endforeach
                            </div>

                        </div>

                    </div>

                </div>
            </section>

        </div>

        @include("{$template_view}.global.footer")

    </div>

    <!-- BEGIN Java Script for this page -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.2.5/jquery.fancybox.min.js"></script>

    <script>
        jQuery(document).ready(function () {
        $(".like").on('click', function(event, value, caption) {    
            $.ajax({
				type: 'GET',
				data: { post_id: '{{ json_encode($post->id) }}' },
				url: '{{ posts_submit_like_url($post->categ_slug, $post->slug) }}',
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

</body>

</html>