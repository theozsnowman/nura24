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

	@if($config->registration_enabled == 0)
	<div class="alert alert-danger">
		{{ __('Warning! Users registration is disabled') }}
	</div>
	@endif

	<form method="post">
		@csrf

		<div class="form-row">
			<div class="form-group col-md-4">
				<label>{{ __('Enable / Disable users registration') }}</label>
				<select name="registration_enabled" class="form-control">
					<option @if (($config->registration_enabled ?? null) == '0') selected @endif value="0">{{ __('Registration Disabled') }}</option>
					<option @if (($config->registration_enabled ?? null) == '1') selected @endif value="1">{{ __('Registration Enabled') }}</option>
				</select>
			</div>	
			
			<div class="form-group col-md-4">
				<label>{{ __('Users must verify email address') }}</label>
				<select name="registration_verify_email_enabled" class="form-control">
					<option @if (($config->registration_verify_email_enabled ?? null) == '1') selected @endif value="1">{{ __('Yes') }}</option>
					<option @if (($config->registration_verify_email_enabled ?? null) == '0') selected @endif value="0">{{ __('No') }}</option>					
				</select>
			</div>	

		</div>
	
		<div class="form-group">
			<button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
		</div>

	</form>

</div>
<!-- end card-body -->