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

	@if($config->site_offline  == 1)
	<div class="alert alert-danger">
		{{ __('Warning! Site is offline') }}
	</div>
	@endif
	
	<form method="post">
		@csrf

		<div class="form-row">

			<div class="form-group col-md-4">
				<label>{{ __('Site online / offline') }}</label>
				<select name="site_offline" class="form-control" aria-describedby="ghelp">
					<option @if ($config->site_offline ?? null == 0) selected @endif value="0">{{ __('Site is Online') }}</option>
					<option @if ($config->site_offline ?? null == 1) selected @endif value="1">{{ __('Site is Offline') }}</option>
				</select>
			</div>

		</div>

		<b>{{ __('Info: If site is offline, you can display a default offline page. File Must be located in active template folder and named "offline.blade.php"') }}</b>

		<div class="form-group">
			<button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
		</div>

	</form>

</div>
<!-- end card-body -->