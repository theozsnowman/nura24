<div class="card-header">
	<h3><i class="fas fa-envelope"></i> {{ __('Email lists') }} ({{ $lists->total() ?? 0 }})</h3>
</div>
<!-- end card-header -->

<div class="card-body">

	<nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.email.campaigns') }}">{{ __('Email campaigns') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Email lists') }}</li>
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
		@if ($message=='duplicate') {{ __('Error. This list exists') }} @endif		
	</div>
	@endif

	<span class="float-right mb-3"><button class="btn btn-primary" data-toggle="modal" data-target="#create-list"><i class="fas fa-plus-square" aria-hidden="true"></i> {{ __('Create email list') }}</button></span>
	@include('admin.email-marketing.modals.create-list')

	<div class="table-responsive-md">
		<table class="table table-bordered table-hover">
			<thead>
				<tr>
					<th width="70">ID</th>
					<th>{{ __('Details') }}</th>				
					<th width="190">{{ __('Recipients') }}</th>
					<th width="200">{{ __('Actions') }}</th>
				</tr>
			</thead>

			<tbody>
				@foreach ($lists as $list)
				<tr>

					<td>
						{{ $list->id}}
					</td>
					<td>									
						<h4>{{ $list->title }}</h4>
					
						@if($list->description)
						{!! nl2br($list->description) !!}
						<br>						
						@endif

						<small class='text-muted'>
						{{ __('Created') }}: {{ date_locale($list->created_at, 'datetime') }} 
						</small>
						<br>					
					</td>
				
					
					<td>
						<h4><a href="{{ route('admin.email.lists.recipients', ['id' => $list->id]) }}">{{ $list->count_recipients }} {{ __('recipients') }}</a></h4>
					</td>
					

					<td>						
						<a href="{{ route('admin.email.lists.recipients', ['id' => $list->id]) }}" class="btn btn-dark btn-block btn-sm mb-2"><i class="far fa-envelope"></i> {{ __('Manage recipients') }}</a>

						<button data-toggle="modal" data-target="#update-list-{{ $list->id }}" class="btn btn-primary btn-block btn-sm mb-2"><i class="fas fa-pen"></i> {{ __('Update list') }}</button>
						@include('admin.email-marketing.modals.update-list')

						<form method="POST" action="{{ route('admin.email.lists.show', ['id' => $list->id]) }}">
							{{ csrf_field() }}
							{{ method_field('DELETE') }}
							<button type="submit" class="float-right btn btn-danger btn-sm btn-block delete-item-{{$list->id}}"><i class="fas fa-trash-alt"></i> {{ __('Delete list') }}</button>
						</form>

						<script>
							$('.delete-item-{{$list->id}}').click(function(e){
									e.preventDefault() // Don't post the form, unless confirmed
									if (confirm('Are you sure to delete this list?')) {
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

	{{ $lists->links() }}

</div>
<!-- end card-body -->