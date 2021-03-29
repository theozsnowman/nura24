<div class="card-header">
    <h3><i class="far fa-edit"></i> {{ __('Update block') }}</h3>
</div>
<!-- end card-header -->

<div class="card-body">

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.blocks') }}">{{ __('Content blocks') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Update block') }}</li>
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

    @if ($message = Session::get('success'))
    <div class="alert alert-info">
        @if ($message=='image_deleted') {{ __('Image deleted') }} @endif
    </div>
    @endif

    <form method="post" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-lg-9">
                <div class="form-group">
                    <label>{{ __('Label') }}</label>
                    <input class="form-control" name="label" type="text" required value="{{ $block->label }}">
                </div>
            </div>

            <div class="col-lg-9">
                <div class="form-group">
                    <label>{{ __('Description') }}</label>
                    <input class="form-control" name="description" type="text" required value="{{ $block->description }}">
                </div>
            </div>

            <div class="col-lg-3">
                <div class="form-group">
                    <label>{{ __('Active') }}</label>
                    <select name="active" class="form-control">
                        <option @if($block->active==1) selected @endif value="1">{{ __('Yes') }}</option>
                        <option @if($block->active==0) selected @endif value="0">{{ __('No') }}</option>
                    </select>
                </div>
            </div>
        </div>

        @foreach($langs as $lang)
        <div class="form-group">
            <label>{{ __('Content') }} @if(count($langs) > 1)- {{ $lang->name }} @if($lang->is_default) ({{ __('default language') }})@endif @endif</label>
            <textarea class="form-control editor" name="content_{{ $lang->id }}">{{ $lang->block_content }}</textarea>
        </div>

        <div class="form-group">
            <label>{{ __('Block image') }} @if(count($langs) > 1)- {{ $lang->name }} @if($lang->is_default) ({{ __('default language') }})@endif @endif</label>
            <div class="custom-file">
                <input type="file" class="custom-file-input" id="validatedCustomFile" name="image_{{ $lang->id }}">
                <label class="custom-file-label" for="validatedCustomFile">{{ __('Choose file') }}...</label>
            </div>

            @if ($lang->block_image)         
            <div class="mt-3"></div>

            <div class="float-left mr-2"><img style="max-width:25px; height:auto;" src="{{ thumb($lang->block_image) }}" /></div>

            <a target="_blank" href="{{ image($lang->block_image) }}">{{ __('View image') }}</a> | 
            <a class="text-danger" href="{{ route('admin.blocks.delete_image', ['id' => $block->id, 'lang_id' => $lang->id]) }}">{{ __('Delete image') }}</a>
            @endif
        </div>

        <div class="mb-4"></div>
        <hr>

        @endforeach


        <div class="form-group">
            <button type="submit" class="btn btn-dark"><i class="fas fa-share"></i> {{ __('Update block') }}</button>
        </div>

    </form>

</div>
<!-- end card-body -->