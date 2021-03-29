<div class="card-header">
	<h3><i class="far fa-file-alt"></i> {{ __('Pages') }} ({{ $pages->total() ?? 0 }})</h3>
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

	@if(check_module('pages'))
	<span class="pull-right"><a href="{{ route('admin.pages.create') }}" class="btn btn-primary"><i class="fas fa-plus-square" aria-hidden="true"></i> {{ __('New page') }}</a></span>
	@endif 

	<section>
		<form action="{{ route('admin.pages') }}" method="get" class="form-inline">
			<input type="text" name="search_terms" placeholder="{{ __('Search pages') }}" class="form-control @if($search_terms) is-valid @endif mr-2" value="{{ $search_terms ?? null }}" />
			<input type="text" name="search_badge" placeholder="{{ __('Search badges') }}" class="form-control @if($search_badge) is-valid @endif mr-2" value="{{ $search_badge ?? null }}" />		

			@if(count(sys_langs()) > 1)
			<select name="search_lang_id" class="form-control @if($search_lang_id) is-valid @endif mr-2">
				<option selected="selected" value="">- {{ __('Any language') }} -</option>
				@foreach (sys_langs() as $lang)
				<option @if($search_lang_id==$lang->id) selected @endif value="{{ $lang->id }}"> {{ $lang->name }}</option>
				@endforeach
			</select>
			@endif

			<button class="btn btn-dark mr-2" type="submit"><i class="fas fa-check"></i> {{ __('Apply') }}</button>
			<a class="btn btn-light" href="{{ route('admin.pages') }}"><i class="fas fa-undo"></i></a>
		</form>
	</section>
	<div class="mb-3"></div>


	<div class="table-responsive-md">
		<table class="table table-bordered table-hover">
			<thead>
				<tr>
					<th width="70">ID</th>
					<th>{{ __('Details') }}</th>				
					@if(count(sys_langs())>1)
					<th width="160">{{ __('Language') }}</th>
					@endif	
					<th width="200">{{ __('Actions') }}</th>
				</tr>
			</thead>

			<tbody>
				@foreach ($pages as $page)
				<tr @if ($page->active==0) class="table-warning" @endif>

					<td>
						{{ $page->id}}
					</td>
					<td>
						@if ($page->active==0)
						<span class="pull-right"><button type="button" class="btn btn-warning btn-sm disabled">{{ __('Draft') }}</button></span>
						@endif

						@if ($page->image)
						<span style="float: left; margin-right:10px;"><img style="max-width:100px; height:auto;" src="{{ thumb($page->image) }}" /></span>
						@endif

						<h4>{{ $page->title }}</h4>

						{{ __('URL') }}: <a target="_blank" href="{{ page_url($page->id) }}">{{ page_url($page->id) }}</a>
						<br>

						@if($page->parent_page_slug)
						<b>{{ __('Parent page') }}</b> <a target="_blank" href="{{ page_url($page->parent_id) }}">{{ $page->parent_page_title }}</a>
						<br>						
						@endif

						@if($page->user_id)
						{{ __('Author') }}: <a target="_blank" href="{{ route('admin.accounts.show', ['id' => $page->user_id]) }}">{{ $page->author_name }}</a>
						<br>						
						@endif

						<small class='text-muted'>
						{{ __('Created') }}: {{ date_locale($page->created_at, 'datetime') }} 
						@if($page->updated_at) | {{ __('Updated') }}: {{ date_locale($page->updated_at, 'datetime') }}@endif
						</small>
						<br>

						@if ($page->redirect_url)
						<b>{{ __('Redirect URL') }}</b>: <a target="_blank" href="{{ urldecode($page->redirect_url)}}">{{ urldecode($page->redirect_url) }}</a><br />
						@endif

						@if ($page->custom_tpl_file)
						<b>{{ __('Custom template file') }}</b>: {{ $page->custom_tpl_file }}<br />
						@endif

						@if ($page->badges)
						<b>{{ __('Badges') }}</b>: {{ $page->badges }}<br>
						@endif

						@if ($page->label)
						<b>{{ __('Label') }}</b>: {{ $page->label }}
						@endif
					</td>
				
					@if(count(sys_langs())>1)
					<td>{{ $page->lang_name ?? __('No language') }}</td>
					@endif

					<td>
						<a href="{{ route('admin.pages.show', ['id' => $page->id]) }}" class="btn btn-primary btn-sm btn-block mb-2"><i class="fas fa-pen"></i> {{ __('Update page') }}</a>

						<a href="{{ route('admin.pages.images', ['id' => $page->id]) }}" class="btn btn-success btn-sm btn-block mb-2"><i class="fas fa-file-image"></i> {{ __('Images gallery') }} ({{ $page->count_images }})</a>

						@if(check_access('pages', 'manager'))
						<form method="POST" action="{{ route('admin.pages.show', ['id' => $page->id]) }}">
							{{ csrf_field() }}
							{{ method_field('DELETE') }}
							<button type="submit" class="float-right btn btn-danger btn-sm btn-block delete-item-{{$page->id}}"><i class="fas fa-trash-alt"></i> {{ __('Delete page') }}</button>
						</form>

						<script>
							$('.delete-item-{{$page->id}}').click(function(e){
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

	{{ $pages->appends(['search_terms' => $search_terms, 'search_badge' => $search_badge, 'search_lang_id' => $search_lang_id])->links() }}

</div>
<!-- end card-body -->