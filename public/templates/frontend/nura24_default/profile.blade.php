<!DOCTYPE html>
<html lang="{{ $locale }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ $user->name }} - {{ site()->short_title }}</title>
    <meta name="description" content="{{ $user->name }} - {{ __('profile page') }}">

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

                            <div>
                                @if($user->avatar) <img src="{{ thumb($user->avatar) }}" alt="{{ $user->name }}" class="img-fluid rounded-circle float-start me-3" style="max-height: 60px;">@endif
                                <h2 class="mb-1">{{ $user->name }}</h2>
                                <div class="text-muted small">{{ __('Registered') }}: {{ date_locale($user->created_at) }}</div>
                            </div>

                            @if($bio)
                            <div class="pofile-bio">{{ $bio }}</div>
                            @endif

                            <hr class="mt-3">

                            @if($posts->total() > 0)

                            <div class="heading">
                                <h3>{{$posts->total() }} {{ __('posts') }}</h3>
                            </div>

                            @foreach ($posts as $post)
                            <div class="mb-2">
                                <i class="fas fa-caret-right"></i> <a title="{{ $post->title }}" href="{{ post_url($post->id) }}">{{ $post->title }}</a>
                                <span class="text-muted">{{ date_locale($post->created_at, 'datetime') }}</span>
                            </div>
                            @endforeach

                            {{ $posts->appends(['id' => $user->id, 'slug' => $user->slug])->links() }}

                            @endif

                        </div>

                    </div>

                </div>

            </section>

        </div>

        @include("{$template_view}.global.footer")

    </div>

</body>

</html>