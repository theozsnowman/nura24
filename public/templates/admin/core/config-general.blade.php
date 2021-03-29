<div class="card-header">
	@include('admin.core.layouts.menu-config')
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

	<form method="post" enctype="multipart/form-data">
		@csrf

		<div class="form-group">

			<div class="row">
			
				<div class="form-group col-12 mt-3">
					<a href="{{ route('admin.config.langs') }}"><b>{{ __('Click here for website homepage settings and SEO') }}</b></a>
				</div>

			</div>

		</div>

		<hr>		

		<div class="form-group row">

			<div class="form-group col-md-4 col-12">
				<label>{{ __('Google Analytics parameter') }} (UA-XXXXX-Y)</label>
				<input type="text" class="form-control" name="google_analytics_ua" aria-describedby="analyticsHelp" value="{{ $config->google_analytics_ua ?? null }}">
				<small id="analyticsHelp" class="form-text text-muted">{{ __('Get this code from') }} <a target="_blank" href="https://google.com/analytics">{{ __('Google Analytics account') }}</a> ({{ __('example') }}: "UA-12345678-1"</small>
			</div>

			<div class="form-group col-md-4 col-12">
				<label>{{ __('Facebook APP ID') }}</label>
				<input type="text" class="form-control" name="facebook_app_id" aria-describedby="fbHelp" value="{{ $config->facebook_app_id ?? null }}">
				<small id="fbHelp" class="form-text text-muted">{{ __('Create Facebook App ID') }}: <a target="_blank" href="https://developers.facebook.com/apps/">{{ __('Facebook for developers') }}</a></small>
			</div>
			
			<div class="form-group col-md-4 col-12">
				<label>{{ __('Site meta author') }}</label>
				<input type="text" class="form-control" name="site_meta_author" value="{{ $config->site_meta_author ?? null }}">				
			</div>
		
		</div>

		<hr>

		<div class="form-group">
			<button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
		</div>

	</form>

</div>
<!-- end card-body -->