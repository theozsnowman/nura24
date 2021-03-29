<div class="card-header">
    <h3 class="float-left"><i class="far fa-edit"></i> {{ __('Update post') }}</h1>    
</div>
<!-- end row -->

<div class="card-body">
    
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.posts') }}">{{ __('Blog') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Update post') }}</li>
        </ol>                                
    </nav>

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
		@if ($message=='main_image_deleted') {{ __('Deleted') }} @endif
	</div>
	@endif 

    <form method="post" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">

            <div class="form-group col-xl-9 col-md-8 col-sm-12">
                <div class="form-group">
                    <label>{{ __('Post title') }}</label>
                    <input class="form-control" name="title" type="text" required value="{{ $post->title }}">
                </div>

                <div class="form-group">
                    <label>{{ __('Summary') }}</label>
                    <textarea rows="3" class="form-control" name="summary">{{ $post->summary }}</textarea>
                </div>

                <div class="form-group">
                    <label>{{ __('Content') }}</label>
                    <textarea class="form-control editor" name="content">{{ $post->content }}</textarea>
                </div>

                <div class="form-group">
                    <label>{{ __('Search terms') }} ({{ __('separated by comma') }})</label>
                    <input type="text" class="form-control" name="search_terms" aria-describedby="searchHelp" value="{{ $post->search_terms }}">
                    <small id="searchHelp" class="form-text text-muted">
                        {{ __("Search terms don't appear on website but they are used to find post in search form") }}
                    </small>
                </div>

                <div class="form-group">
                    <label>{{ __('Change main image') }} ({{ __('optional') }})</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="validatedCustomFile" name="image">
                        <label class="custom-file-label" for="validatedCustomFile">{{ __('Choose file') }}...</label>
                    </div>

                    @if ($post->image)         
                    <div class="mt-3"></div>

                    <div class="float-left mr-2"><img style="max-width:25px; height:auto;" src="{{ thumb($post->image) }}" /></div>

                    <a target="_blank" href="{{ image($post->image) }}">{{ __('View image') }}</a> | 
                    @if(check_access('posts', 'manager'))
                    <a class="text-danger" href="{{ route('admin.posts.delete_main_image', ['id' => $post->id]) }}">{{ __('Delete image') }}</a>
                    @endif
                    @endif

                </div>
            </div>

            <div class="form-group col-xl-3 col-md-4 col-sm-12 border-left">


                <div class="form-group">
                    <label>{{ __('Category') }}</label>
                    <select name="categ_id" class="form-control mr-2" required>
                        <option selected="selected" value="">- {{ __('select') }} -</option>
                        @foreach ($categories as $categ)
                        @include('admin.posts.loops.post-edit-select-loop', $categ)
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>{{ __('Tags') }}</label>
                    <input type="text" class="form-control tagsinput" name="tags" id="tags" value="{{ $tags }}">
                </div>

                <div class="form-group">
                    <label>{{ __('Custom Meta title') }}</label>
                    <input type="text" class="form-control" name="meta_title" aria-describedby="metaTitleHelp" value="{{ $post->meta_title }}">
                    <small id="metaTitleHelp" class="form-text text-muted">
                        {{ __("Leave empty to auto generate meta title based on post title") }}
                    </small>
                </div>

                <div class="form-group">
                    <label>{{ __('Custom Meta description') }}</label>
                    <input type="text" class="form-control" name="meta_description" aria-describedby="metaDescHelp" value="{{ $post->meta_description }}">
                    <small id="metaDescHelp" class="form-text text-muted">
                        {{ __("Leave empty to auto generate meta description based on post summary") }}
                    </small>
                </div>

                <div class="form-group">
                    <label>{{ __('Slug') }}</label>
                    <input type="text" class="form-control" name="slug" aria-describedby="slugHelp" value="{{ $post->slug }}">
                    <small id="slugHelp" class="form-text text-muted">
                        {{ __("Leave empty to auto generate slug based on post title") }}
                    </small>
                </div>

                <div class="form-group">
                    <label>{{ __('Custom template file') }}</label>
                    <input type="text" class="form-control" name="custom_tpl" aria-describedby="tplHelp" value="{{ $post->custom_tpl }}">
                    <small id="tplHelp" class="form-text text-muted">
                        {{ __("You can use a custom template file to display this post. File must be located in template folder. Example: 'custom.blade.php'") }}
                    </small>
                </div>

                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input id="checkbox_disable_comments" type="checkbox" name="disable_comments" class="custom-control-input" @if ($post->disable_comments==1) checked @endif>
                        <label for="checkbox_disable_comments" class="custom-control-label"> {{ __('Disable comments') }}</label>
                    </div>
                </div>

                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input id="checkbox_disable_likes" type="checkbox" name="disable_likes" class="custom-control-input" @if ($post->disable_likes==1) checked @endif>
                        <label for="checkbox_disable_likes" class="custom-control-label"> {{ __('Disable likes') }}</label>
                    </div>
                </div>

                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input id="checkbox_featured" type="checkbox" name="featured" class="custom-control-input" aria-describedby="featuredHelp" @if ($post->featured==1) checked @endif>
                        <label for="checkbox_featured" class="custom-control-label"> {{ __('Featured') }}</label>
                        <small id="featuredHelp" class="form-text text-muted">
                            {{ __("Featured posts are displayed first") }}
                        </small>
                    </div>
                </div>

                <hr />

                <div class="form-group">
                    <button type="submit" name="status" value="active" class="btn btn-dark"><i class="fas fa-share"></i> {{ __('Save and Publish') }}</button>
                    <button type="submit" name="status" value="draft" class="btn btn-light">{{ __('Save draft') }}</button>
                </div>

            </div>

        </div><!-- end row -->

    </form>

</div>
<!-- end card-body -->