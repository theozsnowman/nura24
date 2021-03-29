@for ($i = 1; $i < $loop->depth; ++$i)---@endfor {{ $categ->title }}

@if (count($categ->children) > 0)
@foreach($categ->children as $categ)	
	@include("{$template_view}.loops.docs-categories-loop", $categ)
	@endforeach
@endif
