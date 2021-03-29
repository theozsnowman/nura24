<div class="card-header">
	@include('admin.core.layouts.menu-template')
</div>
<!-- end card-header -->

<div class="card-body">

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
		@if ($message=='updated') {{ __('Updated') }} @endif
	</div>
	@endif

	<span class="float-right">
		<a class="btn btn-dark" target="_blank" href="https://nura24.com/templates"><i class="fas fa-download"></i> {{ __('Download new templates') }}</a>
	</span>

	<h3><i class="fas fa-laptop"></i> {{ __('All templates') }}</h3>

	<hr>

	<div class="card-deck mb-4">
		@foreach ($templates_xml as $template_xml)
		<div class="col-xl-3 col-lg-6 col-md-6 col-12">
			<div class="card card-template">
				<img src="/{{ $template_xml['path'] }}/{{ $template_xml['screenshot'] }}" class="card-img-top" alt="{{ $template_xml['title'] }}">
				<div class="card-body">
					@if($config->template == basename($template_xml['path'])) <span class="float-right btn btn-info btn-sm">{{ __('Active template') }}</span>
					@endif
					<h4 class="card-title">{{ $template_xml['title'] }}</h4>
					<p class="card-text">{{ $template_xml['description'] }}</p>
					<p class="card-text"><small class="text-muted">{{ __('Version') }}: {{ $template_xml['version'] }}</small></p>

					@if($config->template != basename($template_xml['path']))
					<a href="{{ route('admin.config.template.activate', ['template' => basename($template_xml['path'])]) }}" class="btn btn-danger"><i class="fas fa-check"></i> {{ __('Activate template') }}</a>
					@endif
				</div>
			</div>
		</div>
		@endforeach
	</div>

	<div class="row">
		<div class="form-group col-12">
			<a class="btn btn-dark btn-lg" target="_blank" href="https://nura24.com/templates"><i class="fas fa-download"></i> {{ __('Download new templates') }}</a>
		</div>
	</div>

</div>
<!-- end card-body -->