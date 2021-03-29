<div class="card-header">
    <h3 class="float-left"><i class="far fa-plus-square"></i> {{ __('Create post') }}</h3>
</div>
<!-- end row -->

<div class="card-body">       

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.posts') }}">{{ __('Blog') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Create post') }}</li>
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

    @if(! $categories)
	<div class="alert alert-danger mt-3">
		{{ __('Warning. You can not add an post because there is not any categoriy added') }}. <a href="{{ route('admin.posts.categ') }}">{{ __('Manage categories') }}</a>
    </div>
    @else 

    <form action="{{ route('admin.posts') }}" method="post" enctype="multipart/form-data">
        @csrf

        <div class="row">

            <div class="form-group col-xl-9 col-md-8 col-sm-12">
                <div class="form-group">
                    <label>{{ __('Post title') }}</label>
                    <input class="form-control" name="title" type="text" required>
                </div>

                <div class="form-group">
                    <label>{{ __('Summary') }}</label>
                    <textarea rows="3" class="form-control" name="summary"></textarea>
                </div>

                <div class="form-group">
                    <label>{{ __('Content') }}</label>
                    <textarea class="form-control editor" name="content"></textarea>
                </div>

                <div class="form-group">
                    <label>{{ __('Search terms') }} ({{ __('separated by comma') }})</label>
                    <input type="text" class="form-control" name="search_terms" aria-describedby="searchHelp">
                    <small id="searchHelp" class="form-text text-muted">
                        {{ __("Search terms don't appear on website but they are used to find post in search form") }}
                    </small>
                </div>

                <div class="form-group">
                    <label>{{ __('Upload main image') }} ({{ __('optional') }})</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="validatedCustomFile" name="image">
                        <label class="custom-file-label" for="validatedCustomFile">{{ __('Choose file') }}...</label>
                    </div>
                </div>
            </div>

            <div class="form-group col-xl-3 col-md-4 col-sm-12 border-left">                              

                <div class="form-group">
                    <label>{{ __('Category') }}</label>
                    <select name="categ_id" class="form-control mr-2" required>
                        <option value="">- {{ __('select') }} -</option>
                        @foreach ($categories as $categ)
                        @include('admin.posts.loops.categories-add-select-loop', $categ)
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>{{ __('Tags') }}</label>
                    <input type="text" class="form-control tagsinput" name="tags" id="tags">
                </div>
                
                <div class="form-group">
                    <label>{{ __('Custom Meta title') }}</label>
                    <input type="text" class="form-control" name="meta_title" aria-describedby="metaTitleHelp">
                    <small id="metaTitleHelp" class="form-text text-muted">
                        {{ __("Leave empty to auto generate meta title based on post title") }}
                    </small>
                </div>

                <div class="form-group">
                    <label>{{ __('Custom Meta description') }}</label>
                    <input type="text" class="form-control" name="meta_description" aria-describedby="metaDescHelp">
                    <small id="metaDescHelp" class="form-text text-muted">
                        {{ __("Leave empty to auto generate meta description based on post summary") }}
                    </small>
                </div>

                <div class="form-group">
                    <label>{{ __('Slug') }}</label>
                    <input type="text" class="form-control" name="slug" aria-describedby="slugHelp">
                    <small id="slugHelp" class="form-text text-muted">
                        {{ __("Leave empty to auto generate slug based on post title") }}
                    </small>
                </div>

                <div class="form-group">
                    <label>{{ __('Custom template file') }}</label>
                    <input type="text" class="form-control" name="custom_tpl" aria-describedby="tplHelp">
                    <small id="tplHelp" class="form-text text-muted">
                        {{ __("You can use a custom template file to display this post. File must be located in template folder. Example: 'custom.blade.php'") }}
                    </small>
                </div>

                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input id="checkbox_disable_comments" type="checkbox" name="disable_comments" class="custom-control-input">
                        <label for="checkbox_disable_comments" class="custom-control-label"> {{ __('Disable comments') }}</label>
                    </div>
                </div>

                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input id="checkbox_disable_ratings" type="checkbox" name="disable_ratings" class="custom-control-input">
                        <label for="checkbox_disable_ratings" class="custom-control-label"> {{ __('Disable ratings') }}</label>
                    </div>
                </div>

                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input id="checkbox_featured" type="checkbox" name="featured" class="custom-control-input" aria-describedby="featuredHelp">
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
    @endif

</div>
<!-- end card-body -->