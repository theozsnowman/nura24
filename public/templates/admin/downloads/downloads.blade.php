<div class="card-header">
	<h3><i class="fas fa-download"></i> {{ __('Downloads') }} ({{ $downloads->total() ?? 0 }})</h3>
</div>
<!-- end card-header -->

<div class="card-body">

	@if(! check_module('downloads'))
	<div class="alert alert-danger">
		{{ __('Warning. Downloads module is disabled. You can manege content, but module is disabled for visitors and registered users') }}. <a href="{{ route('admin.config.modules') }}">{{ __('Manage modules') }}</a>
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
		@if ($message=='created') {{ __('Created') }} @endif
		@if ($message=='updated') {{ __('Updated') }} @endif
		@if ($message=='deleted') {{ __('Deleted') }} @endif
	</div>
	@endif

	<span class="pull-right"><a href="{{ route('admin.downloads.logs') }}" class="btn btn-dark ml-2"><i class="fas fa-chart-bar" aria-hidden="true"></i> {{ __('Downloads activity') }}</a></span>
	<span class="pull-right"><a href="{{ route('admin.downloads.create') }}" class="btn btn-primary"><i class="fas fa-plus-square" aria-hidden="true"></i> {{ __('Create new download') }}</a></span>

	<section>
		<form action="{{ route('admin.downloads') }}" method="get" class="form-inline">
			<input type="text" name="search_terms" placeholder="{{ __('Search in downloads') }}" class="form-control @if($search_terms) is-valid @endif mr-2" value="{{ $search_terms ?? null }}" />
			<input type="text" name="search_badge" placeholder="{{ __('Search badges') }}" class="form-control @if($search_badge) is-valid @endif mr-2" value="{{ $search_badge ?? null }}" />			

			<button class="btn btn-dark mr-2" type="submit"><i class="fas fa-check"></i> {{ __('Apply') }}</button>
			<a class="btn btn-light" href="{{ route('admin.downloads') }}"><i class="fas fa-undo"></i></a>
		</form>
	</section>
	<div class="mb-3"></div>


	<div class="table-responsive-md">
		<table class="table table-bordered table-hover">
			<thead>
				<tr>
					<th width="40">ID</th>
					<th>{{ __('Details') }}</th>
					<th width="150">{{ __('Downloads') }}</th>
					<th width="200">{{ __('Actions') }}</th>
				</tr>
			</thead>

			<tbody>
				@foreach ($downloads as $download)
				<tr @if ($download->active==0) class="table-warning" @endif>

					<td>
						{{ $download->id }}
					</td>

					<td>
						@if ($download->active==0)
						<span class="pull-right"><button type="button" class="btn btn-warning btn-sm disabled">{{ __('Inactive') }}: </button></span>
						@endif

						@if ($download->login_required==1)
						<span class="pull-right"><button type="button" class="btn btn-warning btn-sm disabled mr-2">{{ __('Login required') }}: </button></span>
						@endif

						@if ($download->image)
						<span style="float: left; margin-right:10px;"><img style="max-width:130px; height:auto;" src="{{ image($download->image) }}" /></span>
						@endif

						<h4><a target="_blank" href="{{ download_url($download->id) }}">{{ $download->title }}</a></h4>
						<div class="mb-2"></div>

						@if ($download->custom_tpl)
						<b>{{ __('Custom template file') }}: </b>: {{ $download->custom_tpl }}<br />
						@endif

						@if ($download->badges)
						<b>{{ __('Badges') }}: </b>: {{ $download->badges }}<br>
						@endif
					</td>

					<td>
						<h5><a href="{{ route('admin.downloads.logs', ['search_download_id' => $download->id]) }}">{{ $download->count_downloads ?? 0 }} {{ __('downloads') }}</a></h5>
					</td>					

					<td>
							<a href="{{ route('admin.downloads.show', ['id' => $download->id]) }}" class="btn btn-primary btn-block btn-sm mb-3"><i class="fas fa-pen"></i> {{ __('Manage download') }}</a>

							<form method="POST" action="{{ route('admin.downloads.show', ['id' => $download->id]) }}">
								{{ csrf_field() }}
								{{ method_field('DELETE') }}
								<button type="submit" class="float-right btn btn-light text-danger btn-sm btn-block delete-item-{{$download->id}}"><i class="fas fa-trash-alt"></i> {{ __('Delete item') }}</button>
							</form>
						</div>

						<script>
							$('.delete-item-{{$download->id}}').click(function(e){
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

	{{ $downloads->appends(['search_terms' => $search_terms, 'search_badge' => $search_badge])->links() }}

</div>
<!-- end card-body -->