<!doctype html>
<html lang="{{ $locale }}">

<head>
    <title>{{ $categ->title}} | {{ lang_meta()->site_short_title }} {{ __('forum') }}</title>
    <meta name="description" content="{{ $categ->description ?? $categ->title}} - {{ lang_meta()->site_short_title }} {{ __('forum') }}">

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

                            @if ($message = Session::get('success'))
                            <div class="alert alert-success">
                                @if ($message=='topic_created') {{ __('Topic created') }} @endif
                            </div>
                            @endif

                            <div class="row">

                                <div class="col-12">

                                    <span class="float-right">
                                        <a class="btn btn-forum ml-4" href="{{ route('forum.topic.create') }}"><i class="fas fa-pen" aria-hidden="true"></i> {{ __('New topic') }}</a>
                                    </span>

                                    <span class="float-right">
                                        <form class="form-inline">
                                            <input class="form-control" name="search" placeholder="{{ __('Search in forum') }}">
                                        </form>
                                    </span>

                                    <div class="clearfix mb-3"></div>

                                    <nav aria-label="breadcrumb">
                                        <ol class="breadcrumb">
                                            <li class="breadcrumb-item"><a href="{{ route('homepage') }}">{{ __('Home') }}</a></li>
                                            <li class="breadcrumb-item"><a href="{{ route('forum') }}">{{ __('Forum') }}</a></li>
                                            @foreach(breadcrumb($categ->id, 'forum') as $b_categ)
                                            <li class="breadcrumb-item"><a href="{{ route('forum.categ', ['slug' => $b_categ->slug]) }}">{{ $b_categ->title }}</a></li>
                                            @endforeach
                                        </ol>
                                    </nav>
                                </div>

                                @if(count(forum_categ_tree($categ->id))>0)
                                <div class="col-12 mb-4">
                                    <div class="card forum-section">

                                        <div class="card-header card-header-forum">
                                            <a class="section-title" title="{{ $categ->title }}" href="{{ route('forum.categ', ['slug' => $categ->slug]) }}">{{ $categ->title }}</a>
                                            @if($categ->description)<br><small>{{ $categ->description }}</small>@endif
                                        </div>

                                        <div class="card-body card-body-forum">
                                            <div class="table-responsive-md">
                                                <table class="table table-forum">
                                                    <tbody>
                                                        @foreach(forum_categ_tree($categ->id) as $level0)
                                                        <tr>
                                                            <td>
                                                                <h5 class="font-weight-bold">
                                                                    <a class="forum-link" title="{{ $level0->title }}" href="{{ route('forum.categ', ['slug' => $level0->slug]) }}">{{ $level0->title }}</a>
                                                                </h5>
                                                                @if($level0->description)<small>{{ $level0->description }}</small><br>@endif
                                                                @foreach ($level0->children as $level1)
                                                                <small class="mr-3">
                                                                    <i class="fas fa-caret-right"></i> <a class="forum-link" title="{{ $level1->title }}"
                                                                        href="{{ route('forum.categ', ['slug' => $level1->slug]) }}">{{ $level1->title }}</a>
                                                                </small>
                                                                @endforeach
                                                            </td>

                                                            <td width="130">
                                                                <div class="text-muted text-small"><b>{{ $level0->count_tree_topics ?? 0 }}</b> {{ __('subjects') }}</div>
                                                                <div class="text-muted text-small"><b>{{ $level0->count_tree_posts ?? 0 }}</b> {{ __('responses') }}</div>
                                                            </td>

                                                            <td width="400">
                                                                @if(!$level0->latest_activity)
                                                                <div class="text-muted text-small">{{ __('No activity') }}</div>
                                                                @endif

                                                                @if($level0->latest_activity=='topic')
                                                                <small>
                                                                    {{ __('Latest topic') }}:
                                                                    <a class="forum-link" title="{{ $level0->latest_topic->title }}"
                                                                        href="{{ route('forum.topic', ['id' => $level0->latest_topic->id, 'slug' => $level0->latest_topic->slug]) }}">{{ substr($level0->latest_topic->title, 0, 40) }}@if(strlen($level0->latest_topic->title)>40)...@endif</a>
                                                                    <br>

                                                                    @if($level0->latest_topic->author_avatar)
                                                                    <img src="{{ thumb($level0->latest_topic->author_avatar) }}" class="forum_user_icon rounded-circle">
                                                                    @else
                                                                    <img src="{{ asset('/assets/img/no-avatar-icon.png') }}" class="forum_user_icon rounded-circle">
                                                                    @endif
                                                                    <b>{{ $level0->latest_topic->author_name}}</b>
                                                                    {{ date_locale($level0->latest_topic->created_at, 'datetime') }}
                                                                </small>
                                                                <div class="mb-2"></div>
                                                                @endif


                                                                @if($level0->latest_activity=='post')
                                                                <small>
                                                                    @if($level0->latest_post->author_avatar)
                                                                    <img src="{{ thumb($level0->latest_post->author_avatar) }}" class="forum_user_icon rounded-circle">
                                                                    @else
                                                                    <img src="{{ asset('/assets/img/no-avatar-icon.png') }}" class="forum_user_icon rounded-circle">
                                                                    @endif
                                                                    <b>{{ $level0->latest_post->author_name}}</b> responded in<br>
                                                                    <a class="forum-link" title="{{ $level0->latest_post->topic_title }}"
                                                                        href="{{ route('forum.topic', ['id' => $level0->latest_post->topic_id, 'slug' => $level0->latest_post->topic_slug]) }}">{{ substr($level0->latest_post->topic_title, 0, 40) }}@if(strlen($level0->latest_post->topic_title)>40)...@endif
                                                                    </a>
                                                                    <span class="text-small text-muted">{{ date_locale($level0->latest_post->created_at, 'datetime') }}</span>
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
                                @endif


                                <div class="col-12 mb-4">
                                    <extrasmall class="mt-0">{{ $categ_topics->links() }}</extrasmall>

                                    <div class="table-responsive-md">
                                        <table class="table table-bordered table-hover">
                                            <tbody>
                                                @foreach ($categ_topics as $topic)
                                                <tr>
                                                    <td>
                                                        <h5 class="font-weight-bold"><a class="forum-link" title="{{ $topic->title }}"
                                                                href="{{ route('forum.topic', ['id' => $topic->id, 'slug' => $topic->slug]) }}">{{ $topic->title }}</a>
                                                            @if($topic->status=='closed')[{{ __('closed') }}]@endif
                                                        </h5>

                                                        @if($topic->author_avatar)
                                                        <img src="{{ thumb($topic->author_avatar) }}" class="forum_user_icon rounded-circle">
                                                        @else
                                                        <img src="{{ asset('/assets/img/no-avatar-icon.png') }}" class="forum_user_icon rounded-circle">
                                                        @endif

                                                        <span class='text-muted text-small'>{{ $topic->author_name }} {{ __('at') }} {{ date_locale($topic->created_at, 'datetime') }}</span>

                                                    </td>

                                                    <td width="130">
                                                        <div class="text-muted text-small"><b>{{ $topic->count_posts }}</b> {{ __('responses') }}</div>
                                                        <div class="text-muted text-small"><b>{{ $topic->hits }}</b> {{ __('views') }}</div>
                                                    </td>

                                                    <td width="400">
                                                        <small>
                                                            @if ($topic->count_posts>0)

                                                            {{ __('Latest response') }}:<br>
                                                            @if($topic->latest_post_author_avatar)
                                                            <img src="{{ thumb($topic->latest_post_author_avatar) }}" class="forum_user_icon rounded-circle">
                                                            @else
                                                            <img src="{{ asset('/assets/img/no-avatar-icon.png') }}" class="forum_user_icon rounded-circle">
                                                            @endif

                                                            {{ $topic->latest_post_author_name }}, <span class="text-small text-muted">{{ date_locale($topic->latest_post_created_at, 'datetime') }}</span>
                                                            <div class="mb-2"></div>
                                                            @else
                                                            {{ __('No response')}}
                                                            @endif
                                                        </small>
                                                    </td>

                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>

                                    </div>

                                    <extrasmall>{{ $categ_topics->links() }}</extrasmall>

                                </div>

                            </div>

                        </div>

                    </div>
                </div>
            </section>

        </div>

        @include("{$template_view}.footer")

    </div>

</body>

</html>