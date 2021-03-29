<div class="card-header">
	<h3><i class="fas fa-th"></i> {{ __('Blocks') }} ({{ $blocks->total() ?? 0 }})</h3>
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

	<span class="pull-right"><a href="{{ route('admin.blocks.create') }}" class="btn btn-primary"><i class="fas fa-plus-square" aria-hidden="true"></i> {{ __('New block') }}</a></span>
	<section>
		<form action="{{ route('admin.blocks') }}" method="get" class="form-inline">
			<input type="text" name="search_terms" placeholder="{{ __('Search block') }}" class="form-control @if($search_terms) is-valid @endif mr-2" value="{{ $search_terms ?? null }}" />

			<button class="btn btn-dark mr-2" type="submit"><i class="fas fa-check"></i> {{ __('Apply') }}</button>
			<a class="btn btn-light" href="{{ route('admin.blocks') }}"><i class="fas fa-undo"></i></a>
		</form>
	</section>
	<div class="mb-3"></div>


	<div class="table-responsive-md">
		<table class="table table-bordered table-hover">
			<thead>
				<tr>
					<th width="80">ID</th>
					<th>{{ __('Details') }}</th>
					<th width="300">{{ __('Template code') }}</th>
					<th width="100">{{ __('Actions') }}</th>
				</tr>
			</thead>

			<tbody>
				@foreach ($blocks as $block)
				<tr @if ($block->active==0) class="table-warning" @endif>

					<td>
						{{ $block->id }}
					</td>

					<td>
						@if ($block->active==0)
						<span class="pull-right"><button type="button" class="btn btn-warning btn-sm disabled">{{ __('Inactive') }}</button></span>
						@endif

						<h5>{{ $block->label }}</h5>

						{{ $block->description }}
					</td>

					<td>
						<pre>{!! block(<?= $block->id ?>) !!}</pre>
						{{ __('or') }}
						<div class="mb-3"></div>
						<pre>{!! block('<?= $block->label ?>') !!}</pre>
					</td>

					<td>

						<div class="d-flex">
							<a href="{{ route('admin.blocks.show', ['id' => $block->id]) }}" class="btn btn-primary btn-sm mr-2"><i class="fas fa-pen"></i></a>

							<form method="POST" action="{{ route('admin.blocks.show', ['id' => $block->id]) }}">
								{{ csrf_field() }}
								{{ method_field('DELETE') }}
								<button type="submit" class="btn btn-danger btn-sm delete-item-{{$block->id}}"><i class="fas fa-trash-alt"></i></button>
							</form>
						</div>

						<script>
							$('.delete-item-{{$block->id}}').click(function(e){
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

	{{ $blocks->appends(['search_terms' => $search_terms])->links() }}

</div>
<!-- end card-body -->