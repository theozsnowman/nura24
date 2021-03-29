<div class="card-header">
	<h3><i class="fas fa-sitemap"></i> {{ __('Tickets departments') }} ({{ $departments-> total() }})</h3>
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
		@if ($message=='created') {{ __('Created') }} @endif
		@if ($message=='updated') {{ __('Updated') }} @endif
		@if ($message=='deleted') {{ __('Deleted') }} @endif
	</div>
	@endif

	@if ($message = Session::get('error'))
	<div class="alert alert-danger">
		@if ($message=='duplicate') {{ __('Error. This department exists') }} @endif
	</div>
	@endif

	<span class="pull-right mb-3"><button data-toggle="modal" data-target="#create-department" class="btn btn-primary"><i class="fas fa-plus-square"></i> {{ __('Create department') }}</button></span>
	@include('admin.tickets.modals.create-department')	

	<div class="table-responsive-md">

		<table class="table table-bordered table-hover">
			<thead>
				<tr>
					<th>{{ __('Details') }}</th>
					<th width="150">{{ __('Tickets') }}</th>				
					<th width="100">{{ __('Actions') }}</th>
				</tr>
			</thead>

			<tbody>

				@foreach ($departments as $department)
				<tr @if ($department->active==0) class="table-warning" @endif>
					<td>
						@if ($department->active==0) <span class="pull-right">&nbsp;<button type="button" class="btn btn-danger btn-sm disabled">{{ __('Inactive') }}</button> </span>@endif
						@if ($department->hidden==1) <span class="pull-right">&nbsp;<button type="button" class="btn btn-info btn-sm disabled">{{ __('Hidden') }}</button> </span>@endif

						<h4>{{ $department->title }}</h4>	
						@if($department->description) <div class="small">{!! nl2br($department->description) !!}</div>@endif
					</td>

					<td>
						<h6>{{ $categ->count_tickets ?? 0 }} {{ __('tickets') }}</h5>
					</td>
			
					<td>
						<div class="d-flex">
							<button data-toggle="modal" data-target="#update-department-{{ $department->id }}" class="btn btn-primary btn-sm mr-2"><i class="fas fa-pen" aria-hidden="true"></i></button>
							@include('admin.tickets.modals.update-department')

							<form method="POST" action="{{ route('admin.tickets.departments.show', ['id' => $department->id]) }}">
								{{ csrf_field() }}
								{{ method_field('DELETE') }}
								<button type="submit" class="float-right btn btn-danger btn-sm delete-item-{{$department->id}}"><i class="fas fa-trash-alt"></i></button>
							</form>							
						</div>

						<script>
							$('.delete-item-{{$department->id}}').click(function(e){
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

	{{ $departments->links() }}

</div>
<!-- end card-body -->