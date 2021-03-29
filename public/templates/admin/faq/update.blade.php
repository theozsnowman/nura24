<div class="card-header">
    <h3><i class="far fa-edit"></i> {{ __('Update') }}</h3>
</div>
<!-- end card-header -->

<div class="card-body">

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.faq') }}">{{ __('FAQ') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Update') }}</li>
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

    <form method="post">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <label>{{ __('Title') }}</label>
                    <input class="form-control" name="title" type="text" required value="{{ $faq->title }}" />
                </div>
            </div>                    

            @if(count(sys_langs())>1)
            <div class="col-md-4">
            <div class="form-group">
                <label>{{ __('Language') }}</label><br />
                <select name="lang_id" class="form-control" required>
                    <option @if(! $faq->lang_id) selected @endif value="">- {{ __('Select') }} -</option>
                    @foreach (sys_langs() as $sys_lang)
                    <option @if($faq->lang_id==$sys_lang->id) selected @endif value="{{ $sys_lang->id }}">{{ $sys_lang->name }}</option>
                    @endforeach
                </select>
            </div>
            </div>
            @endif

            <div class="col-md-4">
                <div class="form-group">
                    <label>{{ __('Active') }}</label>
                    <select name="active" class="form-control">
                        <option @if ($faq->active==1) selected @endif value="1">{{ __('Yes') }}</option>
                        <option @if ($faq->active==0) selected @endif value="0">{{ __('No') }}</option>
                    </select>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label>{{ __('Position') }}</label>
                    <input class="form-control" name="position" type="text" value="{{ $faq->position }}" />
                </div>
            </div>

            <div class="col-12">
                <div class="form-group">
                    <label>{{ __('Content') }}</label>
                    <textarea class="form-control editor trumbowyg-modal" name="content">{{ $faq->content }}</textarea>
                </div>
            </div>

            <div class="col-12">
                <button type="submit" class="btn btn-dark"><i class="fas fa-share"></i> {{ __('Update') }}</button>
            </div>
            
        </div>

    </form>

</div>
<!-- end card-body -->