<a class="anchor" href="#" name="{{ $post->id }}"></a>

<div class="card-header forum-topic-header">
    <b>{{ date_locale($post->created_at, 'datetime') }}</b>
    <span class="float-right font-weight-bold"><a href="{{ route('forum.topic',['id'=>$topic->id, 'slug'=>$topic->slug]) }}#{{ $post->id }}">#{{ $post->id }}</a></span>

</div>

<div class="card-header forum-topic-header-info">
    <span class="float-right text-right">
        {{ __('Registered') }}: {{ date_locale($post->created_at, 'datetime') }}
        <br>
        {{ __('Topics') }}: {{ user_extra($post->user_id, 'count_forum_topics') ?? 0 }}
        <br>
        {{ __('Posts') }}: {{ user_extra($post->user_id, 'count_forum_posts') ?? 0 }}
    </span>

    @if($post->author_avatar)
    <span class="float-left mr-2">
        <img src="{{ thumb($post->author_avatar) }}" class="img-fluid" style="max-width: 80px;">
    </span>
    @endif
    <b>{{ $post->author_name }}</b>
    <br>
    {{ __('Helpful posts') }}: <span @if(user_extra($post->user_id, 'count_forum_likes_received')>10) class="bold text-success" @endif>{{ user_extra($post->user_id, 'count_forum_likes_received') }}</span><br>
    {{ __('Best answer posts') }}: <span @if(user_extra($post->user_id, 'count_forum_best_answers_received')>10) class="bold text-success" @endif>{{ user_extra($post->user_id, 'count_forum_best_answers_received') }}</span>
</div>