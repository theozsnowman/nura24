<div class="card-header">

	@include('admin.core.layouts.menu-tools')

</div>
<!-- end card-header -->

<div class="card-body">

	@if ($message = Session::get('success'))
	<div class="alert alert-success">
		@if ($message=='updated') {{ __('Done') }} @endif
	</div>
	@endif

	<h3>{{ __('Backup') }}</h3>

	<form method="POST">
		{{ csrf_field() }}
		<input type='hidden' name='option' value='db'>
		<button type="submit" class="btn btn-dark">{{ __('Backup database only') }}</button>
	</form>

	<div class="mb-3"></div>

	<form method="POST">
		{{ csrf_field() }}
		<input type='hidden' name='option' value='full'>
		<button type="submit" class="btn btn-dark">{{ __('Full backup (database and files)') }}</button>
	</form>


	<hr>

	@php 
	$path    = storage_path().'/backups/'.env('APP_NAME', 'laravel-backup');
	$files = glob($path.'/*.zip');
	@endphp 

	<h3>{{ __('Backup files are located in') }}: {{ $path }}</h3>
	<div class="mb-2">{{ __('Over time the number of backups and the storage required to store them will grow. At some point you will want to clean up old backups') }}.</div>
	<div class="mb-2 font-weight-bold">{{ __('You have') }} {{ count($files) }} {{__('backup files') }}. </div>
	@if(count($files) > 20)<div class='text-danger font-weight-bold'>{{ __('Please delete old files to save disk space') }}</div>@endif
	<div class="mb-3"></div>

	@php	
	foreach($files as $file) {	
		echo $file;
		echo '<div class="mb-2"></div>';
	}
	@endphp 

</div>
<!-- end card-body -->