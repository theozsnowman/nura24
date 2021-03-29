<div class="card-header">
    <h3><i class="far fa-plus-square"></i> {{ __('Create page') }}</h3>
</div>
<!-- end card-header -->

<div class="card-body">

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.pages') }}">{{ __('Static pages') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Create page') }}</li>
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
        @if ($message=='duplicate') {{ __('Error. Page with this slug already exists') }} @endif
    </div>
    @endif

    <form method="post" action="{{ route('admin.pages') }}" enctype="multipart/form-data">
        @csrf

        <div class="row">

            <div class="form-group col-xl-9 col-md-8 col-sm-12">
                <div class="form-group">
                    <label>{{ __('Page title') }}</label>
                    <input class="form-control" name="title" type="text" required>
                </div>

                <div class="form-group">
                    <label>{{ __('Content') }}</label>
                    <textarea class="form-control editor" name="content"></textarea>
                </div>

                <div class="form-group">
                    <label>{{ __('Upload image') }} ({{ __('optional') }})</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="validatedCustomFile" name="image">
                        <label class="custom-file-label" for="validatedCustomFile">{{ __('Choose file') }}...</label>
                    </div>
                </div>
            </div>

            <div class="form-group col-xl-3 col-md-4 col-sm-12 border-left">

                @if(count(sys_langs())>1)
                <div class="form-group">
                    <label>{{ __('Language') }}</label><br />
                    <select name="lang_id" class="form-control" required>
                        <option selected value="">- {{ __('Select') }} -</option>
                        @foreach (sys_langs() as $lang)
                        <option value="{{ $lang->id }}">{{ $lang->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif                

                <div class="form-group">
                    <label>{{ __('Parent page') }}</label>
                    <select name="parent_id" class="form-control">
                        <option value="">- {{ __('No parent') }} -</option>
                        @foreach ($root_pages as $root_page)
                        <option value="{{ $root_page->id }}">{{ $root_page->title }} @if(count(sys_langs())>1)({{ $root_page->lang_name }})@endif</option>
                        @endforeach
                    </select>
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
                    <input type="text" class="form-control" name="custom_tpl_file">
                </div>

                <div class="form-group">
                    <label>{{ __('Redirect URL') }} ({{ __('optional') }})</label>
                    <input type="text" class="form-control" name="redirect_url">
                </div>

                <div class="form-group">
                    <label>{{ __('Label') }} ({{ __('optional') }})</label>
                    <input type="text" class="form-control" name="label">
                </div>

                <div class="form-group">
                    <label>{{ __('Badges') }} ({{ __('optional') }})</label>
                    <input type="text" class="form-control" name="badges">
                </div>

                <hr />

                <div class="form-group">
                    <button type="submit" name="active" value="1" class="btn btn-dark"><i class="fas fa-share"></i> {{ __('Save and Publish') }}</button>
                    <button type="submit" name="active" value="0" class="btn btn-light">{{ __('Save draft') }}</button>
                </div>

            </div>

        </div><!-- end row -->

    </form>

</div>
<!-- end card-body -->