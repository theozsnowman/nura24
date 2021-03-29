<div class="card-header">
	<h3><i class="fas fa-times"></i> {{ __('Black list') }} - {{ $recipients->total() }} {{ __('recipients') }}</h3>
</div>
<!-- end card-header -->

<div class="card-body">

	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="{{ route('admin') }}">{{ __('Dashboard') }}</a></li>
			<li class="breadcrumb-item"><a href="{{ route('admin.email.campaigns') }}">{{ __('Email campaigns') }}</a></li>
			<li class="breadcrumb-item active">{{ __('Black list') }}</li>
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
	
	<div class="alert alert-info">
		{{ __('Recipients from black list are ignored for all campaigns. They will not receive any campaign email.') }}
	</div>

	<span class="float-right mb-3">
		<button class="btn btn-dark mr-2" data-toggle="modal" data-target="#black-list-add">{{ __('Add recipient in black list') }}</button>
		@include('admin.email-marketing.modals.black-list-add')
	</span>

	<section>
		<form action="{{ route('admin.email.black-list') }}" method="get" class="form-inline">
			<input type="text" name="search_terms" placeholder="{{ __('Search recipient') }}" class="form-control @if($search_terms) is-valid @endif mr-2" value="{{ $search_terms ?? null }}" />

			<button class="btn btn-dark mr-2" type="submit"><i class="fas fa-check"></i> {{ __('Apply') }}</button>
			<a class="btn btn-light" href="{{ route('admin.email.black-list') }}"><i class="fas fa-undo"></i></a>
		</form>
	</section>
	<div class="mb-3"></div>

	<div class="table-responsive-md">
		<table class="table table-bordered table-hover">
			<thead>
				<tr>
					<th>{{ __('Details') }}</th>
					<th width="220">{{ __('Reason') }}</th>
					<th width="160">{{ __('Actions') }}</th>
				</tr>
			</thead>

			<tbody>
				@foreach ($recipients as $recipient)
				<tr>

					<td>
						<h4>{{ $recipient->email }}</h4>						

						<div class='small text-muted mt-2'>
							{{ __('Added') }}: {{ date_locale($recipient->created_at, 'datetime') }}
						</div>
						<br>
					</td>

					<td>
						@if($recipient->reason == 'unsubscribed') {{ __('Unsubscribed') }}@endif
						@if($recipient->reason == 'invalid_email') {{ __('Invalid email') }}@endif
						@if($recipient->reason == 'other') {{ __('Other') }}@endif
					</td>

					<td>
						<form method="POST" action="{{ route('admin.email.black-list.delete', ['id' => $recipient->id]) }}">
							{{ csrf_field() }}
							{{ method_field('DELETE') }}
							<button type="submit" class="float-right btn btn-danger btn-sm btn-block delete-item-{{$recipient->id}}"><i class="fas fa-trash-alt"></i> {{ __('Delete') }}</button>
						</form>

						<script>
							$('.delete-item-{{$recipient->id}}').click(function(e){
									e.preventDefault() // Don't post the form, unless confirmed
									if (confirm('Are you sure to delete this recipient from black list?')) {
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

	{{ $recipients->appends(['search_terms' => $search_terms])->links() }}

</div>
<!-- end card-body -->