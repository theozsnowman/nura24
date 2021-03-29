<div class="card-header">
    <h3><i class="far fa-edit"></i> {{ __('Posts') }} ({{ $posts->total() }})</h3>
</div>
<!-- end card-header -->

<div class="card-body">
    
    @if(! check_module('posts'))
	<div class="alert alert-danger">
		{{ __('Warning. Posts module is disabled. You can manege content, but module is disabled for visitors and registered users') }}. <a href="{{ route('admin.config.modules') }}">{{ __('Manage modules') }}</a>
	</div>
    @endif
    
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
        @if ($message=='created') {{ __('Created') }} @endif
        @if ($message=='updated') {{ __('Updated') }} @endif
        @if ($message=='deleted') {{ __('Deleted') }} @endif
    </div>
    @endif  

    <section>
        @if(check_access('posts'))
        <a href="{{ route('admin.posts.create') }}" class="btn btn-primary mb-2 mr-2"><i class="fas fa-plus-square" aria-hidden="true"></i> {{ __('New post') }}</a>
        @endif

        @if(logged_user()->role == 'admin')
        <a href="{{ route('admin.posts.categ') }}" class="btn btn-dark mr-2 mb-2"><i class="fas fa-sitemap"></i> {{ __('Categories') }}</a>
        @endif

        @if(check_access('posts', 'manager'))
        <a href="{{ route('admin.posts.comments') }}" class="btn btn-dark mr-2 mb-2"><i class="fas fa-comment"></i> {{ __('Comments') }}</a>
        <a href="{{ route('admin.posts.likes') }}" class="btn btn-dark mr-2 mb-2"><i class="fas fa-thumbs-up"></i> {{ __('Likes') }}</a>
        @endif

        @if(logged_user()->role == 'admin')
        <a href="{{ route('admin.posts.config') }}" class="btn btn-dark mb-2"><i class="fas fa-cog"></i></a>
        @endif         
    </section>
    
    <div class="mb-2"></div>

    <section>
        <form action="{{ route('admin.posts') }}" method="get" class="form-inline">
            <input type="text" name="search_terms" placeholder="{{ __('Search in posts') }}" class="form-control mr-2 mb-2 @if($search_terms) is-valid @endif" value="<?= $search_terms;?>" />

            @if(count(sys_langs())>1)
            <select name="search_lang_id" class="form-control @if($search_lang_id) is-valid @endif mr-2 mb-2">
                <option selected="selected" value="">- {{ __('Any language') }} -</option>
                @foreach (sys_langs() as $sys_lang)
                <option @if($search_lang_id==$sys_lang->id) selected @endif value="{{ $sys_lang->id }}"> {{ $sys_lang->name }}</option>
                @endforeach
            </select>
            @endif

            <select name="search_status" class="form-control mr-2 mb-2 @if($search_status) is-valid @endif">
                <option value="">- {{ __('Any status') }} -</option>
                <option @if ($search_status=='active' ) selected="selected" @endif value="active">{{ __('Active') }}</option>
                <option @if ($search_status=='pending' ) selected="selected" @endif value="pending">{{ __('Pending review') }}</option>
                <option @if ($search_status=='draft' ) selected="selected" @endif value="draft">{{ __('Draft') }}</option>
            </select>

            <select class="form-control mr-2 mb-2 @if($search_categ_id) is-valid @endif" name="search_categ_id">
                <option selected="selected" value="">- {{ __('All categories') }} -</option>
                @foreach ($categories as $categ)
                @include('admin.posts.loops.posts-filter-categories-loop', $categ)
                @endforeach
            </select>

            <button class="btn btn-dark mr-2 mb-2" type="submit"><i class="fas fa-check"></i> {{ __('Apply') }}</button>
            <a class="btn btn-light mb-2" href="{{ route('admin.posts') }}"><i class="fas fa-undo"></i></a>
        </form>
    </section>

    <div class="mb-2"></div>

    <div class="table-responsive-md">
        <table class="table table-bordered table-hover">

            <thead>
                <tr>
                    <th>{{ __('Details') }}</th>
                    @if(count(sys_langs())>1)
                    <th width="160">{{ __('Language') }}</th>
                    @endif
                    <th width="320">{{ __('Author') }}</th>
                    <th width="190">{{ __('Interractions') }}</th>
                    <th width="170">{{ __('Actions') }}</th>
                </tr>
            </thead>


            <tbody>
                @foreach ($posts as $post)
                <tr @if ($post->status!='active') class="table-warning" @endif>

                    <td>
                        @if ($post->status=='draft') <span class="pull-right ml-2"><button type="button" class="btn btn-warning btn-sm disabled">{{ __('Draft') }}</button></span> @endif
                        @if ($post->status=='pending') <span class="pull-right ml-2"><button type="button" class="btn btn-danger btn-sm disabled">{{ __('Pending review') }}</button></span> @endif
                        @if ($post->featured==1) <span class="pull-right ml-2"><button type="button" class="btn btn-success btn-sm disabled"><i class="fas fa-thumbtack"></i> {{ __('Featured') }}</button></span>
                        @endif

                        @if ($post->image)
                        <span style="float: left; margin-right:10px;"><img style="max-width:120px; height:auto;" src="{{ thumb($post->image) }}" /></span>
                        @endif
                        <h4>
                            @if($post->lang_status == 'active')<a target="_blank" href="{{ post_url($post->id) }}">{{ $post->title }}</a>
                            @else {{ $post->title }}
                            @endif
                        </h4>
                        <span class="text-muted">
                            {{ __('Created') }}: {{ date_locale($post->created_at, 'datetime') }}
                            @if ($post->updated_at) | {{ __('Updated') }}: {{ date_locale($post->updated_at, 'datetime') }} | @endif
                            {{ $post->hits }} {{ __('hits') }}
                        </span>

                        @if($post->categ_id)
                        <div class="mb-2"></div>
                        {{ __('Category') }}:
                        @foreach(breadcrumb($post->categ_id, 'posts') as $item)
                        <a @if($item->active!=1) class="text-danger" @endif target="_blank" href="{{ posts_url($item->id) }}">{{ $item->title }}</a> @if(!$loop->last) / @endif
                        @endforeach
                        @endif
                      
                    </td>

                    @if(count(sys_langs())>1)
                    <td>
                        {{ $post->lang_name ?? __('No language') }}
                        @if($post->lang_status != 'active') <span class="small text-danger">({{ __('inactive') }})</span>@endif                        
                    </td>
                    @endif

                    <td>
                        @if ($post->author_avatar)
                        <span style="float: left; margin-right:10px;"><img style="max-width:50px; height:auto;" src="{{ image($post->author_avatar) }}" /></span>
                        @endif
                        <b>{{ $post->author_name }}</b>
                        <br>{{ $post->author_email }}
                    </td>

                    <td>
                        @if ($post->count_likes>0)
                        <h5><a href="{{ route('admin.posts.likes', ['search_post_id' => $post->id]) }}"><i class="far fa-thumbs-up"></i> {{ $post->count_likes ?? 0 }} {{ __('likes') }}</a>
                        </h5>
                        @else
                        <i class="far fa-thumbs-up"></i> {{ __('No like') }}
                        @endif
                        <div class="mb-2"></div>
                        @if ($post->count_comments>0)
                        <h5 class="mt-3"><a href="{{ route('admin.posts.comments', ['search_post_id' => $post->id]) }}"><i class="far fa-comment"></i> {{ $post->count_comments ?? 0 }} {{ __('comments') }}</a>
                        </h5>
                        @else
                        <i class="far fa-comment"></i> {{ __('No comments') }}
                        @endif
                    </td>

                    <td>

                        <a href="{{ route('admin.posts.show', ['id' => $post->id]) }}" class="btn btn-primary btn-sm btn-block mb-2"><i class="fas fa-pen"></i> {{ __('Edit post') }}</a>

                        <a href="{{ route('admin.posts.images', ['id' => $post->id]) }}" class="btn btn-info btn-sm btn-block mb-2"><i class="fas fa-file-image"></i> {{ __('Post images') }}
                            ({{ $post->count_images ?? 0 }})</a>                      

                        @if(check_access('posts', 'manager'))
                        <form method="POST" action="{{ route('admin.posts.show', ['id'=>$post->id]) }}">
                            {{ csrf_field() }}
                            {{ method_field('DELETE') }}
                            <button type="submit" class="float-right btn btn-danger btn-sm btn-block delete-item-{{$post->id}}"><i class="fas fa-trash-alt"></i> {{ __('Delete post') }}</button>
                        </form>

                        <script>
                            $('.delete-item-{{$post->id}}').click(function(e){
                                        e.preventDefault() // Don't post the form, unless confirmed
                                        if (confirm("{{ __('Are you sure to delete this post?') }}")) {
                                            $(e.target).closest('form').submit() 
                                        }
                                    });
                        </script>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>

        </table>
    </div>

    {{ $posts->appends(['search_terms' => $search_terms, 'search_status' => $search_status, 'search_categ_id' => $search_categ_id, 'search_lang_id' => $search_lang_id])->links() }}

</div>
<!-- end card-body -->