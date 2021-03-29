<div class="card-header">  
    <h3>{{ $download->title }}</h3>
</div>
<!-- end card-header -->

<div class="card-body">

	<nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.downloads') }}">{{ __('Downloads') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Translates') }}</li>
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
		@if ($message=='created') {{ __('Created') }} @endif
		@if ($message=='updated') {{ __('Updated') }} @endif
		@if ($message=='deleted') {{ __('Deleted') }} @endif
	</div>
	@endif

	<div class="mb-4">
        @include('admin.downloads.layouts.menu-download')
	</div>
	
	<form method="post" action="{{ route('admin.download.translate', ['id' => $download->id]) }}">
        @csrf

        <div class="row">
            <div class="col-12">               

                @foreach($translate_langs as $lang)               
                <div class="form-group">
                    <label>{{ __('Title') }} - {{ $lang->name }}</label>
                    <input class="form-control" type="text" name="title_{{ $lang->id }}" @if($lang->is_default) required @endif value="{{ $lang->translated_title }}">
                </div>

                <div class="form-group">
                    <label>{{ __('Summary') }} ({{ __('optional') }}) - {{ $lang->name }}</label>
                    <textarea class="form-control" rows="3" name="summary_{{ $lang->id }}">{{ $lang->translated_summary }}</textarea>
                </div>
                
                <div class="form-group">
                    <label>{{ __('Content') }} - {{ $lang->name }}</label>
                    <textarea class="form-control editor" name="content_{{ $lang->id }}">{{ $lang->translated_content }}</textarea>
                </div>


                <div class="form-group">
                    <label>{{ __('Meta title') }} - {{ $lang->name }}</label>
                    <input class="form-control" type="text" name="meta_title_{{ $lang->id }}" value="{{ $lang->translated_meta_title }}">
                </div>

                <div class="form-group">
                    <label>{{ __('Meta description') }} - {{ $lang->name }}</label>
                    <textarea class="form-control" rows="2" name="meta_description_{{ $lang->id }}">{{ $lang->translated_meta_description }}</textarea>
                </div>         

                <hr>
                <div class="mb-4"></div>
                @endforeach
            </div>            

        </div>

        <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>

    </form>

</div>
<!-- end card-body -->