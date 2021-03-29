<div class="card-header">
	<h3><i class="fas fa-download"></i> {{ __('Downloads activity') }} ({{ $logs->total() ?? 0 }} {{ __('downloads saved in logs') }})</h3>
</div>
<!-- end card-header -->

<div class="card-body">

	<nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.downloads') }}">{{ __('Downloads') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Downloads activity') }}</li>
        </ol>                                
	</nav>
	
	@if ($message = Session::get('success'))
	<div class="alert alert-success">
		@if ($message=='deleted') {{ __('Deleted') }} @endif
	</div>
	@endif
		
	<div class="table-responsive-md">
		<table class="table table-bordered table-hover">
			<thead>
				<tr>
					<th>{{ __('Files details') }}</th>
					<th width="300">{{ __('Download details') }}</th>
					<th width="350">{{ __('User details') }}</th>
				</tr>
			</thead>

			<tbody>
				@foreach ($logs as $log)
				<tr>
					<td>						
						<h4>{{ $log->download_title }} / {{ $log->download_file_title }}</h4>
						<i class="far fa-file"></i> <a target="_blank" href="{{ asset('uploads/'.$log->download_file) }}">{{ $log->download_file }}</a>
					</td>

					<td>
						<h5>{{ date_locale($log->created_at, 'datetime') }}</h5>	
						IP: {{ $log->ip }}						
					</td>

					<td>
						@if($log->user_id)
							@if ($log->user_avatar)
                        	<span class="float-left mr-2"><img style="max-width:50px; height:auto;" src="{{ image($log->user_avatar) }}" /></span>
                        	@endif
							<a href="{{ route('admin.accounts.show', ['id' => $log->user_id]) }}">{{ $log->user_name }}</a>
							<br>
							{{ $log->user_email }}
						@else
						{{ __('Visitor') }}
						@endif
					</td>										
				</tr>
				@endforeach

			</tbody>
		</table>
	</div>

	{{ $logs->appends(['search_download_id' => $search_download_id, 'search_file_id' => $search_file_id])->links() }}

</div>
<!-- end card-body -->