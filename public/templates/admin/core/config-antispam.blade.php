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

	<h4>{{ __('Google reCAPTCHA') }}</h4>
	<p>{{ __('Create site / secret keys here') }}: <a target="_blank" href="https://www.google.com/recaptcha/admin/create">{{ __('Create Google reCAPTCHA keys') }}</a></p>

	<form method="post">
		@csrf

		<div class="form-row">

			<div class="form-group col-4">
				<label>{{ __('Google reCAPTCHA site key') }}</label>
				<input class="form-control" name="google_recaptcha_site_key" value="{!! $config->google_recaptcha_site_key ?? NULL !!}" />
			</div>

			<div class="form-group col-4">
				<label>{{ __('Google reCAPTCHA secrtet key') }}</label>
				<input type="password" class="form-control" name="google_recaptcha_secret_key" value="{!! $config->google_recaptcha_secret_key ?? NULL !!}" />
			</div>

		</div>

		<hr>

		<div class="form-row">

			<div class="form-group col-md-4">
				<label>{{ __('Enable / Disable Google reCAPTCHA for registration') }}</label>
				<select name="registration_recaptcha_enabled" class="form-control" aria-describedby="ghelp">
					<option @if (($config->registration_recaptcha_enabled ?? null) == 0) selected @endif value="0">{{ __('reCAPTCHA Disabled') }}</option>
					<option @if (($config->registration_recaptcha_enabled ?? null) == 1) selected @endif value="1">{{ __('reCAPTCHA Enabled') }}</option>
				</select>
			</div>

			<div class="form-group col-md-4">
				<label>{{ __('Enable / Disable Google reCAPTCHA for contact page form') }}</label>
				<select name="contact_recaptcha_enabled" class="form-control" aria-describedby="ghelp">
					<option @if (($config->contact_recaptcha_enabled ?? null) == 0) selected @endif value="0">{{ __('reCAPTCHA Disabled') }}</option>
					<option @if (($config->contact_recaptcha_enabled ?? null) == 1) selected @endif value="1">{{ __('reCAPTCHA Enabled') }}</option>
				</select>
			</div>

		</div>

		<div class="form-group">
			<button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
		</div>

	</form>

</div>
<!-- end card-body -->