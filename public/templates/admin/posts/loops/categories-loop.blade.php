<tr @if ($categ->active!=1) class="table-warning" @endif>
	<td>
		@if ($categ->active!=1) <span class="pull-right ml-2"><button type="button" class="btn btn-warning btn-sm disabled">{{ __('Inactive') }}</button></span> @endif
		<h4>@for ($i = 1; $i < $loop->depth; $i++)---@endfor {!! $categ->icon ?? null !!} {{ $categ->title }}</h4>
		<div class="text-muted small">
			ID: {{ $categ->id }} | {{ __('Position') }}: {{ $categ->position }}
			@if($categ->description)<br>{{ $categ->description }}@endif
			@if($categ->badges)<br>{{ __('Badges') }}: {{ $categ->badges }}@endif
		</div>
	</td>

	<td>
		<h4><a href="{{ route('admin.posts', ['search_categ_id'=>$categ->id]) }}">{{ $categ->count_tree_items ?? 0 }} {{ __('posts') }}</a></h4>
	</td>

	@if(count(sys_langs())>1)
	<td>
		{{ lang($categ->lang_id)->name ?? __('No language') }}
	</td>
	@endif

	<td>
		<div class="d-flex">
			@if($categ->slug!='uncategorized')

			<button data-toggle="modal" data-target="#update-categ-{{ $categ->id }}" class="btn btn-primary btn-sm mr-2"><i class="fas fa-pen" aria-hidden="true"></i></button>
			@include('admin.posts.modals.update-categ')
			
			<form method="POST" action="{{ route('admin.posts.categ.show', ['id' => $categ->id]) }}">
				{{ csrf_field() }}
				{{ method_field('DELETE') }}
				<button type="submit" class="float-right btn btn-danger btn-sm delete-item-{{$categ->id}}"><i class="fas fa-trash-alt"></i></button>
			</form>
			@endif
		</div>

		<script>
			$('.delete-item-{{$categ->id}}').click(function(e){
					e.preventDefault() // Don't post the form, unless confirmed
					if (confirm("{{ __('Are you sure to delete this item?') }}")) {
						$(e.target).closest('form').submit() // Post the surrounding form
					}
				});
		</script>
	</td>
</tr>

@if (count($categ->children) > 0)

@foreach($categ->children as $categ)
@include('admin.posts.loops.categories-loop', $categ)
@endforeach

@endif