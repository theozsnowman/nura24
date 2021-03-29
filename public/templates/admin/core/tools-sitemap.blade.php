<div class="card-header">

	@include('admin.core.layouts.menu-tools')

</div>
<!-- end card-header -->

<div class="card-body">	

	@if ($message = Session::get('success'))
	<div class="alert alert-success">
		@if ($message=='updated') {{ __('Sitemap updated') }} @endif
	</div>
	@endif

	{{ __('Sitemap URL') }}: <b>{{ config('app.url') }}/sitemap.xml</b>

	<div class="mb-2"></div>
	<small>{{ __('Sitemap server location') }}:  {{ public_path('sitemap.xml') }}</small>
		
	<hr>

	<form method="POST">
		{{ csrf_field() }}
		<button type="submit" class="btn btn-dark">{{ __('Regenerate XML sitemap file') }}</button>
	</form>
</div>
<!-- end card-body -->