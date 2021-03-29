	<div class="card-header">

		@include('admin.core.layouts.menu-tools')		

	</div>
	<!-- end card-header -->	

	<div class="card-body">
		
		@if ($message = Session::get('success'))
		<div class="alert alert-success">
			@if ($message == 'updated') {{ __('Done') }} @endif
		</div>
		@endif 

		<h4>{{ __('Clear system cache') }}</h4>

		<a href="{{ route('admin.tools.clear_cache', ['section' => 'views']) }}" class="btn btn-danger">{{ __('Clear template files cache') }}</a>

		<a href="{{ route('admin.tools.clear_cache', ['section' => 'routes']) }}" class="btn btn-danger">{{ __('Clear routes cache') }}</a>

		<a href="{{ route('admin.tools.clear_cache', ['section' => 'config']) }}" class="btn btn-danger">{{ __('Clear config cache') }}</a>
								
		<h4 class='mt-4'>{{ __('Clear logs') }}</h4>

		<a href="{{ route('admin.tools.clear_logs', ['section' => 'downloads']) }}" class="btn btn-danger">{{ __('Clear downloads logs older than 30 days') }}</a>

	</div>	
	<!-- end card-body -->								
				
