	<div class="card-header">

		@include('admin.core.layouts.menu-tools')		

	</div>
	<!-- end card-header -->	

	<div class="card-body">
		
		@if ($message = Session::get('success'))
		<div class="alert alert-success">
			@if ($message=='update_available')<h4>{{ __('A new version is available') }}</h4>
			<div class='text-danger font-weight-bold mb-3'>{{ __('You MUST make a backup before upgrading. Go to backup tab to generate a backup of your database and files') }}</div>
			<form method="POST" action="{{ route('admin.tools.update.process') }}">
				{{ csrf_field() }}
				<button type="submit" class="btn btn-success"><i class="fas fa-download"></i> {{ __('Update to latest version') }}</button>
			</form>	
			@endif
			@if ($message=='update_not_available') {{ __('Your software already use latest version') }} @endif
			@if ($message=='updated') {{ __('Your software was updated to latest version') }} @endif
		</div>
		@endif
		
		<div class="mb-3">{{__('Your Nura24 version') }}: <b>{{ config('nura.version') ?? NULL }}</b></div>

		<form method="POST" action="{{ route('admin.tools.update.check') }}">
			{{ csrf_field() }}
			<button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> {{ __('Check for update') }}</button>			
		</form>

		@if($config->last_update_check ?? null)
		<div class="small mt-3">{{ __('Latest update check') }}: {{ date_locale($config->last_update_check, 'datetime') }}</div>
		@endif
										
	</div>	
	<!-- end card-body -->								
				
