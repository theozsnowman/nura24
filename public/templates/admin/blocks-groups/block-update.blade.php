<div class="card-header">
    <h3><i class="far fa-plus-square"></i> {{ __('Update block') }} - {{ $group->title }} </h3>
</div>
<!-- end card-header -->

<div class="card-body">

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.blocks.groups') }}">{{ __('Blocks groups') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.blocks.groups.content', ['id' => $group->id]) }}">{{ $group->title }}</a></li>
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

    <form method="post" action="{{ route('admin.blocks.groups.content.update', ['id' => $group->id, 'block_id' => $block->id]) }}" enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <label>{{ __('Content') }}</label>
            <textarea class="form-control editor" name="content">{{ $block->content}}</textarea>
        </div>

        <div class="form-group">
            <label>{{ __('Block image') }}</label>
            <div class="custom-file">
                <input type="file" class="custom-file-input" id="validatedCustomFile" name="image">
                <label class="custom-file-label" for="validatedCustomFile">{{ __('Choose file') }}...</label>
            </div>

            @if ($block->file)         
            <div class="mt-3"></div>

            <div class="float-left mr-2"><img style="max-width:25px; height:auto;" src="{{ thumb($block->file) }}" /></div>

            <a target="_blank" href="{{ image($block->file) }}">{{ __('View image') }}</a> | 
            <a class="text-danger" href="{{ route('admin.blocks.groups.content.delete_image', ['id' => $group->id, 'block_id' => $block->id]) }}">{{ __('Delete image') }}</a>
            @endif
        </div>

        <div class="row">
            <div class="col-lg-2">
                <div class="form-group">
                    <label>{{ __('Position') }}</label>
                    <input class="form-control" name="position" type="text" value="{{ $block->position }}">
                </div>
            </div>

            <div class="col-lg-2">
                <div class="form-group">
                    <label>{{ __('Active') }}</label>
                    <select name="active" class="form-control">
                        <option @if ($block->active==1) selected @endif value="1">{{ __('Yes') }}</option>
                        <option @if ($block->active==0) selected @endif value="0">{{ __('No') }}</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-dark"><i class="fas fa-share"></i> {{ __('Update block') }}</button>
        </div>

    </form>

</div>
<!-- end card-body -->