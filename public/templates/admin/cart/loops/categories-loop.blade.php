<tr @if ($categ->active!=1) class="table-warning" @endif>
	<td>
		@if ($categ->active!=1) <span class="pull-right ml-2"><button type="button" class="btn btn-warning btn-sm disabled">{{ __('Inactive') }}</button></span> @endif
		<h4>@for ($i = 1; $i < $loop->depth; $i++)---@endfor {!! $categ->icon ?? null !!} {{ $categ->title }}</h4>
		<div class="text-muted small">
			ID: {{ $categ->id }} | {{ __('Position') }}: {{ $categ->position }}
			@if($categ->description)<br>{{ $categ->description }}@endif
			@if($categ->badges)<br>{{ __('Badges') }}: {{ $categ->badges }}@endif							
		</div>

		@if(count($extra_langs) > 0 && $categ->slug!='uncategorized') <a href="{{ route('admin.cart.categ.translate', ['id' => $categ->id]) }}">{{ __('Translate in other languages') }}</a> @endif
	</td>
	
	<td>
		<h5>
			@if($categ->product_type == 'download'){{ __('Downloadable product') }}@endif
			@if($categ->product_type == 'task'){{ __('Task / Service') }}@endif
		</h5>
	</td>

	<td>
		<h4><a href="{{ route('admin.cart.products', ['search_categ_id'=>$categ->id]) }}">{{ $categ->count_tree_items ?? 0 }} {{ __('products') }}</a></h4>
	</td>

	<td>
		@if($categ->slug!='uncategorized')
		<div class="d-flex">
			<button data-toggle="modal" data-target="#update-categ-{{ $categ->id }}" class="btn btn-primary btn-sm mr-2"><i class="fas fa-pen" aria-hidden="true"></i></button>
			@include('admin.cart.modals.update-categ')
					
			<form method="POST" action="{{ route('admin.cart.categ.show', ['id' => $categ->id]) }}">
				{{ csrf_field() }}
				{{ method_field('DELETE') }}
				<button type="submit" class="float-right btn btn-danger btn-sm delete-item-{{$categ->id}}"><i class="fas fa-trash-alt"></i></button>
			</form>			
		</div>

		<script>
			$('.delete-item-{{$categ->id}}').click(function(e){
					e.preventDefault() // Don't post the form, unless confirmed
					if (confirm("{{ __('Are you sure to delete this item?') }}")) {
						$(e.target).closest('form').submit() // Post the surrounding form
					}
				});
		</script>
		@endif
	</td>
</tr>

@if (count($categ->children) > 0)

@foreach($categ->children as $categ)
@include('admin.cart.loops.categories-loop', $categ)
@endforeach

@endif