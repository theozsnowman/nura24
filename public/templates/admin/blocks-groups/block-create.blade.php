<div class="card-header">
    <h3><i class="far fa-plus-square"></i> {{ __('Create block') }} - {{ $group->label }} </h3>
</div>
<!-- end card-header -->

<div class="card-body">

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.blocks.groups') }}">{{ __('Blocks groups') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.blocks.groups.content', ['id' => $group->id]) }}">{{ $group->label }}</a></li>
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

    <form method="post" action="{{ route('admin.blocks.groups.content', ['id' => $group->id]) }}" enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <label>{{ __('Content') }}</label>
            <textarea class="form-control editor" name="content"></textarea>
        </div>

        <div class="form-group">
            <label>{{ __('Block image') }}</label>
            <div class="custom-file">
                <input type="file" class="custom-file-input" id="validatedCustomFile" name="image">
                <label class="custom-file-label" for="validatedCustomFile">{{ __('Choose file') }}...</label>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-2">
                <div class="form-group">
                    <label>{{ __('Position') }}</label>
                    <input class="form-control" name="position" type="text">
                </div>
            </div>

            <div class="col-lg-2">
                <div class="form-group">
                    <label>{{ __('Active') }}</label>
                    <select name="active" class="form-control">
                        <option value="1">{{ __('Yes') }}</option>
                        <option value="0">{{ __('No') }}</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-dark"><i class="fas fa-share"></i> {{ __('Create block') }}</button>
        </div>

    </form>

</div>
<!-- end card-body -->