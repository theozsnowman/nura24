<div class="card-header">
	<h3><i class="fas fa-envelope"></i> {{ $list->title }} - {{ $recipients->total() }} {{ __('recipients') }}</h3>
</div>
<!-- end card-header -->

<div class="card-body">

	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="{{ route('admin') }}">{{ __('Dashboard') }}</a></li>
			<li class="breadcrumb-item"><a href="{{ route('admin.email.lists') }}">{{ __('Email lists') }}</a></li>
			<li class="breadcrumb-item active">{{ $list->title }}</li>
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

	@if ($message = Session::get('error'))
	<div class="alert alert-danger">
		@if ($message=='duplicate') {{ __('Error. This recipient exists') }} @endif
	</div>
	@endif

	<span class="float-right">
		<button class="btn btn-dark mb-2" data-toggle="modal" data-target="#list-import-csv">{{ __('Import CSV') }}</button>
		@include('admin.email-marketing.modals.list-import-csv')
	</span>

	<span class="float-right mb-3">
		<button class="btn btn-dark mr-2" data-toggle="modal" data-target="#list-add-emails">{{ __('Add emails') }}</button>
		@include('admin.email-marketing.modals.list-add-emails')
	</span>

	<section>
		<form action="{{ route('admin.email.lists.recipients', ['id' => $list->id]) }}" method="get" class="form-inline">
			<input type="text" name="search_terms" placeholder="{{ __('Search recipient') }}" class="form-control @if($search_terms) is-valid @endif mr-2" value="{{ $search_terms ?? null }}" />

			<button class="btn btn-dark mr-2" type="submit"><i class="fas fa-check"></i> {{ __('Apply') }}</button>
			<a class="btn btn-light" href="{{ route('admin.email.lists.recipients', ['id' => $list->id]) }}"><i class="fas fa-undo"></i></a>
		</form>
	</section>
	<div class="mb-3"></div>

	<div class="table-responsive-md">
		<table class="table table-bordered table-hover">
			<thead>
				<tr>
					<th>{{ __('Details') }}</th>
					<th width="180">{{ __('Actions') }}</th>
				</tr>
			</thead>

			<tbody>
				@foreach ($recipients as $recipient)
				<tr>

					<td>
						<h4>{{ $recipient->email }}</h4>

						@if($recipient->name)
						{{ $recipient->name }}
						@endif

						<div class='small text-muted mt-2'>
							{{ __('Created') }}: {{ date_locale($recipient->created_at, 'datetime') }}
						</div>
						<br>
					</td>

					<td>
						<form method="POST" action="{{ route('admin.email.lists.recipients.delete', ['id' => $list->id, 'recipient_id' => $recipient->id]) }}">
							{{ csrf_field() }}
							{{ method_field('DELETE') }}
							<button type="submit" class="float-right btn btn-danger btn-sm btn-block delete-item-{{$recipient->id}}"><i class="fas fa-trash-alt"></i> {{ __('Delete recipient') }}</button>
						</form>

						<script>
							$('.delete-item-{{$recipient->id}}').click(function(e){
									e.preventDefault() // Don't post the form, unless confirmed
									if (confirm('Are you sure to delete this recipient?')) {
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

	{{ $recipients->appends(['id' => $list->id, 'search_terms' => $search_terms])->links() }}

</div>
<!-- end card-body -->