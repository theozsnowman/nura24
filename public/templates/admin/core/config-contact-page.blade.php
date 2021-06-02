<div class="card-header">
	@include('admin.core.layouts.menu-config')
</div>
<!-- end card-header -->

<div class="card-body">

	@if(! check_module('contact'))
	<div class="alert alert-danger">
		{{ __('Warning. Contact page module is disabled. You can manege content, but module is disabled for visitors and registered users') }}. <a href="{{ route('admin.config.modules') }}">{{ __('Manage modules') }}</a>
	</div>
	@endif

	@if(! isset($config->contact_form_enabled) || $config->contact_form_enabled != 1)
	<div class="alert alert-danger">
		{{ __('Warning. Contact form is disabled') }}</a>
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
		@if ($message=='updated') {{ __('Updated') }} @endif
	</div>
	@endif

	<form method="post">
		@csrf

		<div class="form-row">			

			<div class="form-group col-md-4">
				<label>{{ __('Enable / disable contact form') }}</label>
				<select name="contact_form_enabled" class="form-control" id="contact_form">
					<option @if (($config->contact_form_enabled ?? null) == 1) selected @endif value="1">{{ __('Contact form enabled') }}</option>
					<option @if (($config->contact_form_enabled ?? null) == 0) selected @endif value="0">{{ __('Contact form disabled') }}</option>
				</select>
			</div>

		</div>

		<hr>

		<div class="form-row">

			<div class="form-group col-md-4">
				<label>{{ __('Enable / disable Google Map') }}</label>
				<select name="contact_map_enabled" class="form-control">
					<option @if (($config->contact_map_enabled ?? null) == 0) selected @endif value="0">{{ __('Google Map disabled') }}</option>
					<option @if (($config->contact_map_enabled ?? null) == 1) selected @endif value="1">{{ __('Google Map enabled') }}</option>
				</select>
			</div>

			<div class="form-group col-4">
				<label>{{ __('Google Map Address') }}</label>
				<input class="form-control" name="contact_map_address" value="{!! $config->contact_map_address ?? null !!}" aria-describedby="maphelp" />
				<small id="maphelp" class="form-text text-muted">{{ __('Map will be centered automatic based on this address. Use complete address (country, region, city, street, code)') }}. {{ __('Example') }}: "Spain, Valencia, Av. de les Balears, 59"</small>
			</div>

		</div>

		<hr>

		{{ __('See also') }}: <a href="{{ route('admin.config.antispam') }}"><b>{{ __('Enable antispam check in contact page') }}</b></a>

		<hr>

		<div class="form-row mt-3">

			<div class="form-group col-12">
				<label>{{ __('Contact page text') }}</label>
				<textarea class="form-control editor" name="contact_page_text">{!! $config->contact_page_text ?? NULL !!}</textarea>
			</div>

		</div>

		<div class="form-group">
			<button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
		</div>

	</form>

</div>
<!-- end card-body -->