<div class="card-header">
	<h3><i class="fas fa-download"></i> {{ $download->title }}</h3>
</div>
<!-- end card-header -->

<div class="card-body">

	<nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin') }}">{{ __('Dashboard') }}</a></li>
			<li class="breadcrumb-item"><a href="{{ route('admin.downloads') }}">{{ __('Downloads') }}</a></li>
			<li class="breadcrumb-item"><a href="{{ route('admin.downloads.show', ['id' => $download->id]) }}">{{ $download->title }}</a></li>
            <li class="breadcrumb-item active">{{ __('Files') }}</li>
        </ol>                                
	</nav>
	
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
		@if ($message=='created') {{ __('Created') }} @endif
		@if ($message=='updated') {{ __('Updated') }} @endif
		@if ($message=='deleted') {{ __('Deleted') }} @endif
	</div>
	@endif

	<div class="mb-4">
		@include('admin.downloads.layouts.menu-download')
	</div>
	
	
	<span class="pull-right mb-3"><button class="btn btn-primary" data-toggle="modal" data-target="#create-file"><i class="fas fa-plus-square" aria-hidden="true"></i> {{ __('Create file') }}</button></span>
	@include('admin.downloads.modals.create-file')

	<div class="table-responsive-md">
		<table class="table table-bordered table-hover">
			<thead>
				<tr>
					<th>{{ __('Details') }}</th>
					<th width="220">{{ __('Release date') }}</th>
					<th width="150">{{ __('File version') }}</th>
					<th width="150">{{ __('Downloads') }}</th>
					<th width="100">{{ __('Actions') }}</th>
				</tr>
			</thead>

			<tbody>
				@foreach ($files as $file)
				<tr @if ($file->active==0) class="table-warning" @endif>
					<td>
						@if ($file->active==0)
						<span class="pull-right"><button type="button" class="btn btn-warning btn-sm disabled">{{ __('Inactive') }}</button></span>
						@endif

						@if ($file->featured==1)
						<span class="pull-right"><button type="button" class="btn btn-warning btn-sm disabled">{{ __('Featured') }}</button></span>
						@endif

						<h4>{{ $file->title }}</h4>
						<i class="far fa-file"></i> <a target="_blank" href="{{ asset('uploads/files/'.$file->file) }}">{{ $file->file }}</a>
					</td>

					<td>
						<h5>@if($file->release_date){{ date_locale($file->release_date) }}@endif</h5>
					</td>

					<td>
						<h5>{{ $file->version ?? null }}</h5>
					</td>

					<td>
						<h5><a href="{{ route('admin.downloads.logs', ['search_file_id' => $file->id]) }}">{{ $file->count_downloads ?? 0 }} {{ __('downloads') }}</a></h5>
					</td>

					<td>
						<div class="d-flex">
							<button data-toggle="modal" data-target="#update-file-{{ $file->id }}" class="btn btn-primary btn-sm mr-2"><i class="fas fa-pen" aria-hidden="true"></i></button>
							@include('admin.downloads.modals.update-file')

							<form method="POST" action="{{ route('admin.download.files.delete', ['id' => $download->id, 'file_id' => $file->id]) }}">
								{{ csrf_field() }}
								{{ method_field('DELETE') }}
								<button type="submit" class="float-right btn btn-danger btn-sm delete-item-{{$file->id}}"><i class="fas fa-trash-alt"></i></button>
							</form>
						</div>

						<script>
							$('.delete-item-{{$file->id}}').click(function(e){
									e.preventDefault() // Don't post the form, unless confirmed
									if (confirm('Are you sure to delete this item?')) {
										$(e.target).closest('form').submit() // Post the surrounding form
									}
								});
						</script>
					</td>
				</tr>
				@endforeach

			</tbody>
		</table>
	</div>

	{{ $files->appends(['id' => $download->id])->links() }}

</div>
<!-- end card-body -->