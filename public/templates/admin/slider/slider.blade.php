<div class="card-header">
	<h3><i class="far fa-file-image"></i> {{ __('Homepage slider') }}</h3>
</div>
<!-- end card-header -->

<div class="card-body">

	@if(! check_module('slider'))
	<div class="alert alert-danger">
		{{ __('Warning. Slider is disabled. You can manege content, but module is disabled for visitors and registered users') }}. <a href="{{ route('admin.config.modules') }}">{{ __('Manage modules') }}</a>
	</div>
	@else
	<div class="alert alert-info">
		{{ __('Slider is enabled') }}. <a href="{{ route('admin.config.modules') }}">{{ __('Manage modules') }}</a>
	</div>
    @endif
    	
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

	<div class="row">
		<div class="col-md-4 col-12">

			<form method="post" enctype="multipart/form-data" action="{{ route('admin.slider.config') }}">
				@csrf
							
				<div class="form-group">
					<label>{{ __('Change main background') }}</label>
					<div class="custom-file">
						<input type="file" class="custom-file-input" name="main_bg" aria-describedby="fileHelp">
						<label class="custom-file-label" for="validatedCustomFile">{{ __('Choose file...') }}</label>
					</div>
				</div>

				<div class="form-group">
					<label>{{ __('Slider background color') }}</label>
					<input type="color" id="color" class="form-control" name="slider_background_color" value="{{ $config->slider_background_color ?? '$ffffff' }}" aria-describedby="bgColorHelp">
					<small id="bgColorHelp" class="form-text text-muted">{{ ('This will be used if no slides are active (only background image is set)') }}</small>
				</div>

				<button type="submit" class="btn btn-dark"><i class="fas fa-share"></i> {{ __('Update') }}</button>

			</form>
		</div>

		<div class="col-md-8 col-xs-12">
			<label>{{ __('Slider main background') }}</label><br>
			@if ($config->slider_main_background ?? null)<img src="/uploads/{{ $config->slider_main_background }}" class="img-fluid">@endif
		</div>

	</div>

	<hr class="mb-3">

	<h3>{{ __('Slider items') }} ({{ $slides->total() }})</h3>

	<span class="float-right mb-3"><button class="btn btn-primary" data-toggle="modal" data-target="#create"><i class="fas fa-plus-square" aria-hidden="true"></i> {{ __('Create slide') }}</button></span>
	@include('admin.slider.modals.create')

	@if(count(sys_langs())>1)
	<section>
		<form action="{{ route('admin.slider') }}" method="get" class="form-inline">
			<select name="search_lang_id" class="form-control @if($search_lang_id) is-valid @endif mr-2">
				<option selected="selected" value="">- {{ __('Any language') }} -</option>
				@foreach (sys_langs() as $lang)
				<option @if($search_lang_id==$lang->id) selected @endif value="{{ $lang->id }}"> {{ $lang->name }}</option>
				@endforeach
			</select>

			<button class="btn btn-dark mr-2" type="submit"><i class="fas fa-check"></i> {{ __('Apply') }}</button>
			<a class="btn btn-light" href="{{ route('admin.slider') }}"><i class="fas fa-undo"></i></a>
		</form>
	</section>
	<div class="mb-3"></div>
	@endif

	<div class="table-responsive-md">
		<table class="table table-bordered table-hover">

			<thead>
				<tr>
					<th width="160">{{ __('Image') }}</th>
					<th>{{ __('Details') }}</th>
					@if(count(sys_langs())>1)
					<th width="160">{{ __('Language') }}</th>
					@endif
					<th width="100">{{ __('Actions') }}</th>
				</tr>
			</thead>

			<tbody>
				@foreach ($slides as $slide)
				<tr @if ($slide->active==0) class="table-warning" @endif>

					<td>
						@if ($slide->image)
						<img style="max-width:160px; height:auto;" src="{{ thumb($slide->image) }}" />
						@endif
					</td>

					<td>
						@if ($slide->active==0)<span class="pull-right"><button type="button" class="btn btn-warning btn-sm disabled">{{ __('Inactive') }}</button></span> @endif
						<h5>{{ $slide->title }}</h5>
						@if ($slide->content) {!! $slide->content !!}<br> @endif
						<small>
							@if ($slide->url) {{ __('Link') }}: <a target="_blank" href="{{ $slide->url }}">{{ $slide->url }}</a><br> @endif
						</small>
					</td>

					@if(count(sys_langs())>1)
					<td>{{ $slide->lang_name ?? __('No language') }}</td>
					@endif

					<td>
						<div class="d-flex">
							<button data-toggle="modal" data-target="#update-{{ $slide->id }}" class="btn btn-primary btn-sm mr-2"><i class="fas fa-pen"></i></button>
							@include('admin.slider.modals.update')

							<form method="POST" action="{{ route('admin.slider.show', ['id' => $slide->id]) }}">
								{{ csrf_field() }}
								{{ method_field('DELETE') }}
								<button type="submit" class="float-right btn btn-danger btn-sm delete-item-{{$slide->id}}"><i class="fas fa-trash-alt"></i></button>
							</form>
						</div>

						<script>
							$('.delete-item-{{$slide->id}}').click(function(e){
									e.preventDefault() // Don't post the form, unless confirmed
									if (confirm('{{ __("Are you sure to delete this item?") }}')) {
										$(e.target).closest('form').submit() // Post the surrounding form
									}
								});
						</script>
					</td>
				</tr>
				@endforeach
			</tbody>

		</table>
	</div>

	{{ $slides->appends(['search_lang_id' => $search_lang_id])->links() }}

</div>
<!-- end card-body -->