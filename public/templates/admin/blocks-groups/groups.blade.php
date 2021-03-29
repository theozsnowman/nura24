<div class="card-header">
	<h3><i class="far fa-file-image"></i> {{ __('Blocks groups') }} ({{ $groups->total() }})</h3>
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
		@if ($message=='duplicate') {{ __('Error. This group exists') }} @endif
	</div>
	@endif


	<span class="float-right mb-3"><button class="btn btn-primary" data-toggle="modal" data-target="#create"><i class="fas fa-plus-square" aria-hidden="true"></i> {{ __('Create group') }}</button></span>
	@include('admin.blocks-groups.modals.create')

	<div class="table-responsive-md">
		<table class="table table-bordered table-hover">

			<thead>
				<tr>
					<th width="50">ID</th>
					<th>{{ __('Details') }}</th>
					<th width="220">{{ __('Images') }}</th>
					<th width="100">{{ __('Actions') }}</th>
				</tr>
			</thead>

			<tbody>
				@foreach ($groups as $group)
				<tr @if ($group->active==0) class="table-warning" @endif>

					<td>
						{{ $group->id }}
					</td>

					<td>
						@if ($group->active==0)<span class="pull-right"><button type="button" class="btn btn-warning btn-sm disabled">{{ __('Inactive') }}</button></span> @endif
						<h5>{{ $group->label }}</h5>
						{!! nl2br($group->description) !!}
					</td>

					<td>
						<a href="{{ route('admin.blocks.groups.content', ['id' => $group->id]) }}" class="btn btn-dark btn-sm btn-block mb-2"><i class="fas fa-file"></i> {{ __('Manage blocks') }} ({{ $group->count_blocks }})</a>
					</td>

					<td>
						<div class="d-flex">
							<button data-toggle="modal" data-target="#update-{{ $group->id }}" class="btn btn-primary btn-sm mr-2"><i class="fas fa-pen"></i></button>
							@include('admin.blocks-groups.modals.update')

							<form method="POST" action="{{ route('admin.blocks.groups', ['id' => $group->id]) }}">
								{{ csrf_field() }}
								{{ method_field('DELETE') }}
								<button type="submit" class="btn btn-danger btn-sm delete-item-{{$group->id}}"><i class="fas fa-trash-alt"></i></button>
							</form>
						</div>

						<script>
							$('.delete-item-{{$group->id}}').click(function(e){
									e.preventDefault() // Don't post the form, unless confirmed
									if (confirm('{{ __("Are you sure to delete this group?") }}')) {
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

	{{ $groups->links() }}

</div>
<!-- end card-body -->