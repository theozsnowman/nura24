<div class="card-header">
    <h3><i class="fas fa-download"></i> {{ ('Create download') }}</h3>
</div>
<!-- end card-header -->

<div class="card-body">

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.downloads') }}">{{ __('Downloads') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Create download') }}</li>
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

    @if ($message = Session::get('error'))
    <div class="alert alert-danger">
        @if ($message=='error_title') {{ ('Error. Input ') }} @endif
        @if ($message=='duplicate') {{ ('Error. This slug already exist') }} @endif
    </div>
    @endif   

    <form method="post" action="{{ route('admin.downloads') }}" enctype="multipart/form-data">
        @csrf

        <div class="row">

            <div class="form-group col-xl-9 col-md-8 col-sm-12">
                <div class="form-group">
                    <label>{{ __('Title') }}</label>
                    <input class="form-control" name="title" type="text" required>
                </div>
                
                <div class="form-group">
                    <label>{{ __('Summary') }}</label>
                    <textarea class="form-control" name="summary"></textarea>
                </div>

                <div class="form-group">
                    <label>{{ __('Content') }}</label>
                    <textarea class="form-control editor" name="content"></textarea>
                </div>               

            </div>

            <div class="form-group col-xl-3 col-md-4 col-sm-12 border-left">             
                
                <div class="form-group">
                    <label>{{ __('Image') }} ({{ __('optional') }})</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="validatedCustomFile" name="image">
                        <label class="custom-file-label" for="validatedCustomFile">{{ __('Choose file') }}...</label>
                    </div>
                </div>

                <div class="form-group">
                    <label>{{ __('Custom Meta title') }} ({{ __('optional') }})</label>
                    <input type="text" class="form-control" name="meta_title">
                </div>

                <div class="form-group">
                    <label>{{ __('Custom Meta description') }} ({{ __('optional') }})</label>
                    <input type="text" class="form-control" name="meta_description">
                </div>

                <div class="form-group">
                    <label>{{ __('Custom URL structure') }} ({{ __('optional') }})</label>
                    <input type="text" class="form-control" name="slug">
                </div>

                <div class="form-group">
                    <label>{{ __('Custom template file') }} ({{ __('optional') }})</label>
                    <input type="text" class="form-control" name="custom_tpl">
                </div>

                <div class="form-group">
                    <label>{{ __('Badges') }} ({{ __('optional') }})</label>
                    <input type="text" class="form-control" name="badges">
                </div>

                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input id="checkbox_login_required" type="checkbox" name="login_required" class="custom-control-input" aria-describedby="loginHelp">
                        <label for="checkbox_login_required" class="custom-control-label"> {{ __('Only registered users can download') }}</label>
                        <small id="loginHelp" class="form-text text-muted">{{ __('If checked, only registered and logged users cand download files') }}</small>
                    </div>
                </div>

                <hr />

                <div class="form-group">
                    <button type="submit" name="active" value="1" class="btn btn-dark"><i class="fas fa-share"></i> {{ __('Save and Publish') }}</button>
                    <button type="submit" name="active" value="0" class="btn btn-light">{{ __('Save draft') }}</button>
                </div>

                <div class="alert alert-info">
                    {{ __('After you add download, you can manage files') }}
                </div>

            </div>

        </div><!-- end row -->

    </form>

</div>
<!-- end card-body -->