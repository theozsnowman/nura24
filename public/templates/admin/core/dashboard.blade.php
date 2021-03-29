<div class="card-header">
    <h3><i class="fas fa-bars"></i> {{ __('Dashboard') }} </h3>
</div>
<!-- end card-header -->

<div class="card-body">

    @if (!empty($config->site_offline))
    @if($config->site_offline=='yes')
    <div class="alert alert-danger">
        {{ __('Site is offline') }}. <a href="{{ route('admin.config.site_offline') }}">{{ __('Change') }}</a>
    </div>
    @endif
    @endif

    @if ($message = Session::get('error'))
    <div class="alert alert-danger">
        @if ($message=='demo') {{ __('Error. This action is disabled in demo mode') }} @endif
    </div>
    @endif       

    <div class="row">
        <div class="col-12 col-md-6 col-lg-6 col-xl-3">
            <div class="card-box noradius noborder bg-default">
                <i class="fas fa-user float-right text-white"></i>
                <h6 class="text-white text-uppercase m-b-20">{{ __('Accounts') }}</h6>
                <h2 class="m-b-20 text-white counter">{{ $count_accounts ?? 0 }} {{ __('total') }}</h2>
                <span class="text-white">{{ $count_accounts_today ?? 0 }} {{ __('today') }}, {{ $count_accounts_last_month ?? 0 }} {{ __('last month') }}</span>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-6 col-xl-3">
            <div class="card-box noradius noborder bg-success">
                <i class="fas fa-ticket-alt float-right text-white"></i>
                <h6 class="text-white text-uppercase m-b-20">{{ __('Tickets') }}</h6>
                <h2 class="m-b-20 text-white counter">{{ $count_pending_tickets ?? 0 }} {{ __('waiting') }}</h2>
                <span class="text-white">{{ $count_open_tickets ?? 0 }} {{ __('open tickets') }}</span>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-6 col-xl-3">
            <div class="card-box noradius noborder bg-danger">
                <i class="fas fa-envelope-open float-right text-white"></i>
                <h6 class="text-white text-uppercase m-b-20">{{ __('Inbox') }}</h6>
                <h2 class="m-b-20 text-white counter">{{ $count_inbox_unread ?? 0 }} {{ __('unread') }}</h2>
                <span class="text-white">{{ $count_inbox ?? 0 }} {{ __('total messages') }}</span>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-6 col-xl-3">
            <div class="card-box noradius noborder bg-dark">
                <i class="fas fa-shopping-cart float-right text-white"></i>
                <h6 class="text-white text-uppercase m-b-20">{{ __('Orders') }}</h6>
                <h2 class="m-b-20 text-white counter">{{ $count_unpaid_orders?? 0 }} {{ __('unpaid') }}</h2>
                <span class="text-white">{{ $count_paid_orders_last_month ?? 0 }} {{ __('paid last 30 days') }}, {{ $count_paid_orders ?? 0 }} {{ __('total paid') }}</span>
            </div>
        </div>

    </div>
    <!-- end row -->



    <div class="row">

        <div class="col-12 col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <div class="float-right mt-1"><a href="{{ route ('admin.inbox') }}" class="btn btn-sm btn-light">{{ __('View all') }}</a></div>
                    <h3><i class="fas fa-envelope"></i> {{ __('Latest messages') }}</h3>
                </div>

                <div class="card-body bg-light">
                    <div class="widget-messages nicescroll" style="height: 400px;">
                        @foreach ($latest_inbox as $msg)
                        <a href="{{ route ('admin.inbox.show', ['id'=>$msg->id]) }}">
                            <div class="message-item">
                                <p class="message-item-user">
                                    {{ $msg->name }} ({{ $msg->email }})
                                    <div class="text-muted small">
                                        {{ substr($msg->message, 0, 200) }}...
                                        <br>
                                        {{ date_locale($msg->created_at) }}
                                    </div>
                                </p>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>


        <div class="col-12 col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <div class="float-right mt-1"><a href="{{ route ('admin.accounts') }}" class="btn btn-sm btn-light">{{ __('View all') }}</a></div>
                    <h3><i class="fas fa-user"></i> {{ __('Latest accounts') }}</h3>
                </div>

                <div class="card-body bg-light">
                    <div class="widget-messages nicescroll" style="height: 400px;">
                        @foreach ($latest_accounts as $account)
                        <a target="_blank" href="{{ route('admin.accounts.show', ['id' => $account->id]) }}">
                            <div class="message-item">
                                <p class="message-item-user">
                                    @if($account->avatar)
                                    <span class="float-left mr-3"><img alt="{{ $account->name}}" style="height: 80px; height: 80px;" src="{{ image($account->avatar) }}" /></span>
                                    @endif
                                    {{ $account->name}}
                                    <br>
                                    <span class="text-muted small">
                                        {{ $account->email }}
                                        <br>
                                        {{ __('Registered') }}: {{ date_locale($account->created_at, 'datetime') }}
                                    </span>
                                </p>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

    </div>


    <div class="row">
        <div class="col-xs-12 col-md-6 col-lg-6 col-xl-3">
            <div class="card-box noradius noborder bg-default">
                <i class="far fa-comment-alt float-right text-white"></i>
                <h6 class="text-white text-uppercase m-b-20">Topics created last 24 hours</h6>
                <h1 class="m-b-20 text-white counter">{{ $count_forum_topics_latest_24h ?? 0 }}</h1>
                <span class="text-white">{{ $count_forum_topics ?? 0 }} total topics</span>
            </div>
        </div>

        <div class="col-xs-12 col-md-6 col-lg-6 col-xl-3">
            <div class="card-box noradius noborder bg-warning">
                <i class="far fa-comments float-right text-white"></i>
                <h6 class="text-white text-uppercase m-b-20">Posts created last 24 hours</h6>
                <h1 class="m-b-20 text-white counter">{{ $count_forum_posts_latest_24h ?? 0 }}</h1>
                <span class="text-white">{{ $count_forum_posts ?? 0 }} total posts</span>
            </div>
        </div>
        

        <div class="col-xs-12 col-md-6 col-lg-6 col-xl-3">
            <div class="card-box noradius noborder bg-danger">
                <i class="fas fa-exclamation-triangle float-right text-white"></i>
                <h6 class="text-white text-uppercase m-b-20">Reports pending</h6>
                <h1 class="m-b-20 text-white counter">{{ $count_forum_unprocessed_reports }}</h1>
                <span class="text-white">{{ $count_forum_reports_latest_24h }} reports latest 24 hours</span>
            </div>
        </div>
    </div>
    <!-- end row -->

    
    <div class="row">

        <div class="col-12 col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <div class="float-right mt-1"><a href="{{ route ('admin.forum.topics') }}" class="btn btn-sm btn-light">{{ __('View all') }}</a></div>
                    <h3><i class="fas fa-comments"></i> {{ __('Latest forum topics') }}</h3>
                </div>

                <div class="card-body bg-light">
                    <div class="widget-messages nicescroll" style="height: 400px;">
                        @foreach ($latest_forum_topics as $topic)
                        <a target="_blank" href="{{ route('forum.topic', ['id' => $topic->id, 'slug' => $topic->slug]) }}">
                            <div class="message-item">
                                <p class="message-item-user">
                                    @if($topic->author_avatar)
                                    <span class="float-left mr-2"><img alt="{{ $topic->author_name}}" class="logged_user_avatar rounded-circle" style="max-height: 20px;" src="{{ image($topic->author_avatar) }}" /></span>
                                    @endif
                                    {{ $topic->author_name}}

                                    {{ $topic->title }}
                                    <div class="text-muted small">
                                        {{ substr(strip_tags($topic->content), 0, 250) }}...
                                        <br>
                                        {{ date_locale($topic->created_at, 'datetime') }}
                                    </div>
                                </p>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>


        <div class="col-12 col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <div class="float-right mt-1"><a href="{{ route ('admin.forum.posts') }}" class="btn btn-sm btn-light">{{ __('View all') }}</a></div>
                    <h3><i class="fas fa-comments"></i> {{ __('Latest forum posts') }}</h3>
                </div>

                <div class="card-body bg-light">
                    <div class="widget-messages nicescroll" style="height: 400px;">
                        @foreach ($latest_forum_posts as $post)
                        <a target="_blank" href="{{ route('forum.post', ['topic_id' => $post->topic_id, 'slug' => $post->topic_slug, 'post_id' => $post->id]) }}">
                            <div class="message-item">
                                <p class="message-item-user">
                                    @if($post->author_avatar)
                                    <span class="float-left mr-2"><img alt="{{ $post->author_name}}" class="logged_user_avatar rounded-circle" style="max-height: 20px;" src="{{ image($post->author_avatar) }}" /></span>
                                    @endif
                                    {{ $post->author_name}}

                                    <br>
                                    {{ __('In topic') }}: {{ $post->topic_title }}
                                    <div class="text-muted small">
                                        {{ substr(strip_tags($post->content), 0, 250) }}...
                                        <br>
                                        {{ date_locale($post->created_at, 'datetime') }}
                                    </div>
                                </p>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

    </div>


</div>