<div class="card-header">
    <h3><i class="fas fa-bars"></i> Forum Dashboard</h3>
</div>
<!-- end card-header -->

<div class="card-body">

    @if (!empty($config->site_offline))
    @if($config->site_offline=='yes')
    <div class="alert alert-danger">
        Warning! Site is offline. <a href="/admin/config/site-offline">Change</a>
    </div>
    @endif
    @endif

    @if ($message = Session::get('error'))
    <div class="alert alert-danger">
        @if ($message=='demo') Error! There action is disabled in demo mode @endif
    </div>
    @endif


    <div class="row">
        <div class="col-xs-12 col-md-6 col-lg-6 col-xl-3">
            <div class="card-box noradius noborder bg-default">
                <i class="far fa-comment-alt float-right text-white"></i>
                <h6 class="text-white text-uppercase m-b-20">Topics created last 24 hours</h6>
                <h1 class="m-b-20 text-white counter">{{ $count_topics_latest_24h ?? 0 }}</h1>
                <span class="text-white">{{ $count_topics ?? 0 }} total topics</span>
            </div>
        </div>

        <div class="col-xs-12 col-md-6 col-lg-6 col-xl-3">
            <div class="card-box noradius noborder bg-warning">
                <i class="far fa-comments float-right text-white"></i>
                <h6 class="text-white text-uppercase m-b-20">Posts created last 24 hours</h6>
                <h1 class="m-b-20 text-white counter">{{ $count_posts_latest_24h ?? 0 }}</h1>
                <span class="text-white">{{ $count_posts ?? 0 }} total posts</span>
            </div>
        </div>

        <div class="col-xs-12 col-md-6 col-lg-6 col-xl-3">
            <div class="card-box noradius noborder bg-info">
                <i class="fas fa-dollar-sign float-right text-white"></i>
                <h6 class="text-white text-uppercase m-b-20">Amount earned</h6>
                <h1 class="m-b-20 text-white counter">{{ $count_amount ?? 0 }}</h1>
                <span class="text-white">{{ $count_amount_last_month ?? 0 }} earned last month</span>
            </div>
        </div>

        <div class="col-xs-12 col-md-6 col-lg-6 col-xl-3">
            <div class="card-box noradius noborder bg-danger">
                <i class="fas fa-exclamation-triangle float-right text-white"></i>
                <h6 class="text-white text-uppercase m-b-20">Reports pending</h6>
                <h1 class="m-b-20 text-white counter">{{ $count_unprocessed_reports }}</h1>
                <span class="text-white">{{ $count_reports_latest_24h }} reports latest 24 hours</span>
            </div>
        </div>
    </div>
    <!-- end row -->


    <div class="row">

        <div class="col-12 col-sm-12 col-md-6">

            <div class="table-responsive-md">

                <table class="table table-bordered table-hover">

                    <thead>
                        <tr>
                            <th>
                                <a class="float-right btn btn-light btn-sm" href="{{ route('admin.forum.topics') }}">View all</a>
                                <h4>Latest topics</h4>
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($latest_topics as $topic)
                        @if($loop->index < 5) <tr>
                            <td>
                                <div class="float-right">
                                    <div class="text-muted text-small">{{ date_locale($topic->created_at, 'datetime') }}</div>
                                </div>

                                <h4><a target="_blank" href="{{ route('forum.topic', ['id' => $topic->id, 'slug' => $topic->slug]) }}">{{ $topic->title }}</a></h4>

                                <div class="mb-2">
                                    @if($topic->author_avatar) <img class="logged_user_avatar rounded-circle" style="max-height:20px" src="{{ thumb($topic->author_avatar) }}">@endif
                                    {{ $topic->author_name}}
                                </div>

                                <div class="text-muted text-small">{{ substr(strip_tags($topic->content), 0, 250) }}...</div>

                            </td>
                            </tr>
                            @endif
                            @endforeach
                    </tbody>
                </table>
            </div>

        </div>


        <div class="col-12 col-sm-12 col-md-6">

            <div class="table-responsive-md">

                <table class="table table-bordered table-hover">

                    <thead>
                        <tr>
                            <th>
                                <a class="float-right btn btn-light btn-sm" href="{{ route('admin.forum.posts') }}">View all</a>
                                <h4>Latest posts</h4>
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($latest_posts as $post)
                        @if($loop->index < 5) <tr>
                            <td>
                                <div class="float-right">
                                    <div class="text-muted text-small">{{ date_locale($post->created_at, 'datetime') }}</div>
                                </div>

                                <h4><a target="_blank" href="{{ route('forum.post', ['topic_id' => $post->topic_id, 'slug' => $post->topic_slug, 'post_id' => $post->id]) }}">{{ $post->topic_title }}</a></h4>

                                <div class="mb-2">
                                    @if($post->author_avatar) <img class="logged_user_avatar rounded-circle" style="max-height:20px" src="{{ thumb($post->author_avatar) }}">@endif
                                    {{ $post->author_name}}
                                </div>

                                <div class="text-muted text-small">{{ substr(strip_tags($post->content), 0, 250) }}...</div>

                            </td>
                            </tr>
                            @endif
                            @endforeach
                    </tbody>
                </table>
            </div>

        </div>


    </div>

</div>