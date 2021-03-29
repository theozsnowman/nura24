<div class="card-header">
	<h3><i class="far fa-file-image"></i> {{ __('Blocks') }} - {{ $group->label }} ({{ $blocks-> total() }})</h3>
</div>
<!-- end card-header -->

<div class="card-body">

	<nav aria-label="breadcrumb">
		<ol class="breadcrumb mb-3">
			<li class="breadcrumb-item"><a href="{{ route('admin') }}">{{ __('Dashboard') }}</a></li>
			<li class="breadcrumb-item"><a href="{{ route('admin.blocks.groups') }}">{{ __('Blocks groups') }}</a></li>
			<li class="breadcrumb-item"><a href="{{ route('admin.blocks.groups.content', ['id' => $group->id]) }}">{{ $group->label }}</a></li>
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

	<span class="pull-right mb-3"><a href="{{ route('admin.blocks.groups.content.create', ['id' => $group->id]) }}" class="btn btn-primary"><i class="fas fa-plus-square"></i> {{ __('Add block') }}</a></span>

	<div class="table-responsive-md">

		<table class="table table-bordered table-hover">
			<thead>
				<tr>
					<th width="50">{{ __('Position') }}</th>
					<th width="200">{{ __('Image') }}</th>
					<th>{{ __('Content') }}</th>
					<th width="200">{{ __('Actions') }}</th>
				</tr>
			</thead>

			<tbody>

				@foreach ($blocks as $block)
				<tr @if ($block->active==0) class="table-warning" @endif>
					<td>
						{{ $block->position }}
					</td>

					<td>						
						@if ($block->file)
						<a target="_blank" href="{{ image($block->file) }}"><img style="max-width:200px; height:auto;" src="{{ thumb($block->file) }}" /></a>
						@endif
					</td>

					<td>
						@if ($block->active==0)<span class="pull-right"><button type="button" class="btn btn-warning btn-sm disabled">{{ __('Inactive') }}</button></span> @endif
						{!! $block->content ?? null !!}
					</td>

					<td>
						<a href="{{ route('admin.blocks.groups.content.show', ['id' => $group->id, 'block_id' => $block->id]) }}" class="btn btn-primary btn-sm btn-block mb-2"><i class="fas fa-pen"></i> {{ __('Update block') }}</a>

						<form method="POST" action="{{ route('admin.blocks.groups.content', ['id' => $group->id, 'block_id' => $block->id]) }}">
							{{ csrf_field() }}
							{{ method_field('DELETE') }}
							<button type="submit" class="btn btn-danger btn-sm btn-block delete-item-{{$block->id}}"><i class="fas fa-trash-alt"></i> {{ __('Delete block') }}</button>
						</form>

						<script>
							$('.delete-item-{{$block->id}}').click(function(e){
									e.preventDefault() // Don't post the form, unless confirmed
									if (confirm("{{ __('Are you sure to delete this item?') }}")) {
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

	{{ $blocks->appends(['id' => $group->id])->links() }}

</div>
<!-- end card-body -->