<!doctype html>
<html lang="{{ $locale }}">

<head>
    <title>{{ __('Community forum') }} - {{ site()->short_title }}</title>
    <meta name="description" content="Forum">

    @include("{$template_view}.global.head")

</head>

<body>

    <div id="all">

        <div id="content-wrap">

            @include("{$template_view}.global.navigation")

            <section>

                <div class="container">

                    <div class="row">

                        <div class="col-12">

                            @if ($message = Session::get('success'))
                            <div class="alert alert-success">
                                @if ($message=='topic_created') {{ __('Topic created') }} @endif
                            </div>
                            @endif

                            <div class="row">

                                <div class="col-12 mb-1">

                                    <div class="float-end">
                                        <a class="btn btn-forum ms-4" href="{{ route('forum.topic.create') }}"><i class="fas fa-pen" aria-hidden="true"></i> {{ __('New topic') }}</a>
                                    </div>

                                    <div class="float-start">
                                        <form class="form-inline">
                                            <input class="form-control" name="s" placeholder="{{ __('Search in forum') }}">
                                        </form>
                                    </div>

                                    <div class="clearfix mb-3"></div>

                                    <nav aria-label="breadcrumb" class="forum-breadcrumb">
                                        <ol class="breadcrumb">
                                            <li class="breadcrumb-item"><a href="{{ homepage() }}">{{ __('Home') }}</a></li>
                                            <li class="breadcrumb-item active"><a href="{{ forum_url() }}">{{ __('Forum') }}</a></li>
                                        </ol>
                                    </nav>
                                </div>

                                @foreach(forum_categ_tree() as $section)
                                <div class="col-12 mb-4">
                                    <div class="forum-section">

                                        <div class="card-header card-header-forum">
                                            {!! $section->icon ?? null !!} <a class="section-title" title="{{ $section->title }}" href="{{ route('forum.categ', ['slug' => $section->slug]) }}">{{ $section->title }}</a>
                                            @if($section->description)<br><small>{{ $section->description }}</small>@endif
                                        </div>

                                        <div class="card-body card-body-forum">
                                            <div class="table-responsive-md">
                                                <table class="table table-forum">
                                                    <tbody>
                                                        @foreach($section->children as $categ)
                                                        <tr>
                                                            <td>
                                                                <h5 class="font-weight-bold">{!! $categ->icon ?? null !!} <a class="forum-link" title="{{ $categ->title }}"
                                                                        href="{{ route('forum.categ', ['slug' => $categ->slug]) }}">{{ $categ->title }}</a>
                                                                </h5>
                                                                @if($categ->description)<small>{{ $categ->description }}</small><br>@endif
                                                                @foreach($categ->children as $subcateg)
                                                                <small class="mr-3">
                                                                    <i class="fas fa-caret-right"></i> <a class="forum-link" title="{{ $subcateg->title }}"
                                                                        href="{{ route('forum.categ', ['slug' => $subcateg->slug]) }}">{{ $subcateg->title }}</a>
                                                                </small>
                                                                @endforeach
                                                            </td>

                                                            <td width="130">
                                                                <div class="text-muted text-small"><b>{{ $categ->count_tree_topics ?? 0 }}</b> {{ __('subjects') }}</div>
                                                                <div class="text-muted text-small mb-2"><b>{{ $categ->count_tree_posts ?? 0 }}</b> {{ __('responses') }}</div>
                                                            </td>

                                                            <td width="400">
                                                                @if(!$categ->latest_activity)
                                                                <div class="text-muted text-small">{{ __('No activity') }}</div>
                                                                @endif

                                                                @if($categ->latest_activity=='topic')
                                                                <small>

                                                                    @if($categ->latest_topic->author_avatar)
                                                                    <img src="{{ thumb($categ->latest_topic->author_avatar) }}" class="forum_user_icon rounded-circle">
                                                                    @else
                                                                    <img src="{{ asset('/assets/img/no-avatar-icon.png') }}" class="forum_user_icon rounded-circle">
                                                                    @endif
                                                                    <b>{{ $categ->latest_topic->author_name}}</b>

                                                                    {{ __('created new topic') }}:<br>
                                                                    <a class="forum-link" title="{{ $categ->latest_topic->title }}"
                                                                        href="{{ route('forum.topic', ['id' => $categ->latest_topic->id, 'slug' => $categ->latest_topic->slug]) }}">{{ substr($categ->latest_topic->title, 0, 40) }}@if(strlen($categ->latest_topic->title)>40)...@endif
                                                                    </a> {{ __('at') }}
                                                                    <span class="text-small text-muted">{{ date_locale($categ->latest_topic->created_at, 'datetime') }}</span>
                                                                </small>
                                                                <div class="mb-2"></div>
                                                                @endif


                                                                @if($categ->latest_activity=='post')
                                                                <small>
                                                                    @if($categ->latest_post->author_avatar)
                                                                    <img src="{{ thumb($categ->latest_post->author_avatar) }}" class="forum_user_icon rounded-circle">
                                                                    @else
                                                                    <img src="{{ asset('/assets/img/no-avatar-icon.png') }}" class="forum_user_icon rounded-circle">
                                                                    @endif
                                                                    <b>{{ $categ->latest_post->author_name}}</b> {{ __('responded in') }}<br>
                                                                    <a class="forum-link" title="{{ $categ->latest_post->topic_title }}"
                                                                        href="{{ route('forum.topic', ['id' => $categ->latest_post->topic_id, 'slug' => $categ->latest_post->topic_slug]) }}">{{ substr($categ->latest_post->topic_title, 0, 40) }}@if(strlen($categ->latest_post->topic_title)>40)...@endif
                                                                    </a> {{ __('at') }}
                                                                    <span class="text-small text-muted">{{ date_locale($categ->latest_post->created_at, 'datetime') }}</span>
                                                                </small>
                                                                @endif
                                                            </td>

                                                        </tr>
                                                        @endforeach
                                                </table>
                                            </div>
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

        @include("{$template_view}.global.footer")

    </div>

</body>

</html>