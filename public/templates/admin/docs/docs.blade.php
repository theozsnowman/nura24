<div class="card-header">
	<h3><i class="far fa-file-alt"></i> {{ __('Articles') }} ({{ $docs->total() }})</h3>
</div>
<!-- end card-header -->

<div class="card-body">

	@if(! check_module('docs'))
	<div class="alert alert-danger">
		{{ __('Warning. Knowledge Base module is disabled. You can manege content, but module is disabled for visitors and registered users') }}. <a href="{{ route('admin.config.modules') }}">{{ __('Manage modules') }}</a>
	</div>
	@endif
	
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb mb-3">
			<li class="breadcrumb-item"><a href="{{ route('admin') }}">{{ __('Dashboard') }}</a></li>
			<li class="breadcrumb-item"><a href="{{ route('admin.docs') }}">{{ __('Knowledge Base') }}</a></li>
			<li class="breadcrumb-item active">{{ __('Articles') }}</li>
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

	<span class="pull-right"><a href="{{ route('admin.docs.create') }}" class="btn btn-primary"><i class="fas fa-plus-square" aria-hidden="true"></i> {{ __('Create new article') }}</a></span>

	@if(logged_user()->role == 'admin')
	<span class="pull-right"><a href="{{ route('admin.docs.categ') }}" class="btn btn-dark mr-2"><i class="fas fa-folder" aria-hidden="true"></i> {{ __('Manage categories') }}</a></span>
	@endif 
	
	<section>
		<form action="{{ route('admin.docs') }}" method="get" class="form-inline">

			<input class="form-control mr-2 @if($search_terms) is-valid @endif" name="search_terms" value="{{ $search_terms }}">

			<select class="form-control mr-2 @if($search_categ_id) is-valid @endif" name="search_categ_id">
				<option selected="selected" value="">- {{ __('All categories') }} -</option>
				@foreach ($categories as $categ)
				@include('admin.docs.loops.posts-filter-categories-loop', $categ)
				@endforeach
			</select>

			@if(count(sys_langs())>1)
			<select name="search_lang_id" class="form-control @if($search_lang_id) is-valid @endif mr-2">
				<option selected="selected" value="">- {{ __('Any language') }} -</option>
				@foreach (sys_langs() as $sys_lang)
				<option @if($search_lang_id==$sys_lang->id) selected @endif value="{{ $sys_lang->id }}"> {{ $sys_lang->name }}</option>
				@endforeach
			</select>
			@endif

			<button class="btn btn-dark mr-2" type="submit"><i class="fas fa-check"></i> {{ __('Apply') }}</button>
			<a class="btn btn-light" href="{{ route('admin.docs') }}"><i class="fas fa-undo"></i></a>
		</form>
	</section>
	<div class="mb-3"></div>


	<div class="table-responsive-md">
		<table class="table table-bordered table-hover">
			<thead>
				<tr>
					<th>{{ __('Details') }}</th>
					@if(count(sys_langs())>1)
					<th width="160">{{ __('Language') }}</th>
					@endif
					<th width="180">{{ __('Actions') }}</th>
				</tr>
			</thead>

			<tbody>
				@foreach ($docs as $doc)
				<tr @if ($doc->active==0) class="table-warning" @endif>
					<td>
						@if ($doc->featured==1)
						<span class="pull-right"><button type="button" class="btn btn-success btn-sm disabled">{{ __('Featured') }}</button></span>
						@endif

						@if ($doc->active==0)
						<span class="pull-right"><button type="button" class="btn btn-warning btn-sm disabled">{{ __('Draft') }}</button></span>
						@endif

						@if($doc->categ_id)
						<h4><a target="_blank" href="{{ route('docs.categ', ['lang' => prepend_lang($doc->lang_id), 'slug' => $doc->categ_slug]) }}#{{ $doc->slug}}">{{ $doc->title }}</a></h4>
						@else
						<h4>{{ $doc->title }}</h4>
						@endif

						@if($doc->categ_id)
						<div class="mb-2"></div>
						{{ __('Category') }}:
						@foreach(breadcrumb($doc->categ_id, 'docs') as $item)
						<a @if($item->active!=1) class="text-danger" @endif target="_blank" href="{{ route('docs.categ', ['lang' => prepend_lang($doc->lang_id), 'slug' => $item->slug])}}">{{ $item->title }}</a>
						@if(!$loop->last) / @endif
						@endforeach
						@endif

						<div class="small">
							{{ __('Position') }}: {{ $doc->position }}
						</div>
					</td>

					@if(count(sys_langs())>1)
					<td>{{ $doc->lang_name ?? __('Any language') }}</td>
					@endif

					<td>
						<a href="{{ route('admin.docs.show', ['id' => $doc->id]) }}" class="btn btn-primary btn-block btn-sm mb-2"><i class="fas fa-pen"></i> {{ __('Edit article') }}</a>

						<a href="{{ route('admin.docs.images', ['id' => $doc->id]) }}" class="btn btn-info btn-sm btn-block mb-2"><i class="fas fa-file-image"></i> {{ __('Article images') }}
							({{ $doc->count_images ?? 0 }})</a>

						@if(check_access('docs', 'manager'))
						<form method="POST" action="{{ route('admin.docs.show', ['id' => $doc->id]) }}">
							{{ csrf_field() }}
							{{ method_field('DELETE') }}
							<button type="submit" class="float-right btn btn-danger btn-sm btn-block delete-item-{{$doc->id}}"><i class="fas fa-trash-alt"></i> {{ __('Delete') }}</button>
						</form>

						<script>
							$('.delete-item-{{$doc->id}}').click(function(e){
									e.preventDefault() // Don't post the form, unless confirmed
									if (confirm('Are you sure to delete this item?')) {
										$(e.target).closest('form').submit() // Post the surrounding form
									}
								});
						</script>
						@endif 
					</td>
				</tr>
				@endforeach

			</tbody>
		</table>
	</div>

	{{ $docs->appends(['search_categ_id' => $search_categ_id, 'search_lang_id' => $search_lang_id, 'search_terms' => $search_terms])->links() }}

</div>
<!-- end card-body -->