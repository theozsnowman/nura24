<tr @if ($categ->active!=1) class="table-warning" @endif>
	<td>
		@if ($categ->active!=1) <span class="pull-right ml-2"><button type="button" class="btn btn-warning btn-sm disabled">{{ __('Inactive') }}</button></span> @endif
		<h4>@for ($i = 1; $i < $loop->depth; $i++)---@endfor {!! $categ->icon ?? null !!} {{ $categ->title }}</h4>
		<div class="text-muted small">
			ID: {{ $categ->id }} | {{ __('Position') }}: {{ $categ->position }}
			<br>
			{{ __('Allow topics') }}: @if($categ->allow_topics==1){{ __('Yes') }} @else {{ __('No') }}@endif
			@if($categ->description)<br>{{ $categ->description }}@endif
			@if($categ->badges)<br>{{ __('Badges') }}: {{ $categ->badges }}@endif
		</div>
	</td>

	<td>
		@if ($categ->type == 'discussion') <i class="far fa-comment-alt"></i> {{ __('Discussion') }} @endif
		@if ($categ->type == 'question') <i class="far fa-question-circle"></i> {{ __('Question & Answers') }} @endif
	</td>

	<td>
		<h4><a href="{{ route('admin.forum.topics', ['search_categ_id'=>$categ->id]) }}">{{ $categ->count_tree_topics ?? 0 }} {{ __('topics') }}</a></h4>
		<h4><a href="{{ route('admin.forum.posts', ['search_categ_id'=>$categ->id]) }}">{{ $categ->count_tree_posts ?? 0 }} {{ __('posts') }}</a></h4>
	</td>

	<td>
		<div class="d-flex">
			<button data-toggle="modal" data-target="#update-categ-{{ $categ->id }}" class="btn btn-primary btn-sm mr-2"><i class="fas fa-pen" aria-hidden="true"></i></button>
			@include('admin.forum.modals.update-categ')

			@if($categ->slug!='uncategorized')
			<form method="POST" action="{{ route('admin.forum.categ.show', ['id' => $categ->id]) }}">
				{{ csrf_field() }}
				{{ method_field('DELETE') }}
				<button type="submit" class="float-right btn btn-danger btn-sm delete-item-{{$categ->id}}"><i class="fas fa-trash-alt"></i></button>
			</form>
			@endif
		</div>

		<script>
			$('.delete-item-{{$categ->id}}').click(function(e){
					e.preventDefault() // Don't post the form, unless confirmed
					if (confirm("{{ __('Are you sure to delete this category?') }}")) {
						$(e.target).closest('form').submit() // Post the surrounding form
					}
				});
		</script>
	</td>
</tr>

@if (count($categ->children) > 0)

@foreach($categ->children as $categ)
@include('admin.forum.loops.categories-loop', $categ)
@endforeach

@endif