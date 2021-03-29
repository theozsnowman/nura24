<div class="card-header">
    <h3><i class="far fa-plus-square"></i> {{ __('Create block') }}</h3>
</div>
<!-- end card-header -->

<div class="card-body">

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.blocks') }}">{{ __('Content blocks') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Create block') }}</li>
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
        @if ($message=='duplicate') {{ __('Error. This block exists') }} @endif
    </div>
    @endif

    <form method="post" action="{{ route('admin.blocks') }}" enctype="multipart/form-data">
        @csrf

        <div class="row">

            <div class="col-12">
                <div class="form-group">
                    <label>{{ __('Label') }}</label>
                    <input class="form-control" name="label" type="text" required>
                </div>
            </div>

            <div class="col-lg-9">
                <div class="form-group">
                    <label>{{ __('Description') }}</label>
                    <input class="form-control" name="description" type="text">
                </div>
            </div>

            <div class="col-lg-3">
                <div class="form-group">
                    <label>{{ __('Active') }}</label>
                    <select name="active" class="form-control">
                        <option value="1">{{ __('Yes') }}</option>
                        <option value="0">{{ __('No') }}</option>
                    </select>
                </div>
            </div>
        </div>

        <hr>

        @foreach(sys_langs() as $lang)
        <div class="form-group">
            <label>{{ __('Content') }} @if(count(sys_langs()) > 1)- {{ $lang->name }} @if($lang->is_default) ({{ __('default language') }})@endif @endif</label>
            <textarea class="form-control editor" name="content_{{ $lang->id }}"></textarea>
        </div>

        <div class="form-group">
            <label>{{ __('Block image') }} @if(count(sys_langs()) > 1)- {{ $lang->name }} @if($lang->is_default) ({{ __('default language') }})@endif @endif</label>
            <div class="custom-file">
                <input type="file" class="custom-file-input" id="validatedCustomFile" name="image_{{ $lang->id }}">
                <label class="custom-file-label" for="validatedCustomFile">{{ __('Choose file') }}...</label>
            </div>
        </div>

        <div class="mb-4"></div>
        <hr>

        @endforeach

        <div class="form-group">
            <button type="submit" class="btn btn-dark"><i class="fas fa-share"></i> {{ __('Create block') }}</button>
        </div>

    </form>

</div>
<!-- end card-body -->