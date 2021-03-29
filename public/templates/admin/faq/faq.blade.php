<div class="card-header">
	<h3><i class="far fa-file-image"></i> {{ __('FAQ') }}</h3>
</div>
<!-- end card-header -->

<div class="card-body">

	@if(! check_module('faq'))
	<div class="alert alert-danger">
		{{ __('Warning. FAQ module is disabled. You can manege content, but module is disabled for visitors and registered users') }}. <a href="{{ route('admin.config.modules') }}">{{ __('Manage modules') }}</a>
	</div>
	@endif
	
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

	@if ($message = Session::get('danger'))
	<div class="alert alert-danger">
		@if ($message=='error_title') {{ __('Error. Input title') }} @endif
	</div>
	@endif

	<span class="pull-right"><a href="{{ route('admin.faq.create') }}" class="btn btn-primary"><i class="fas fa-plus-square" aria-hidden="true"></i> {{ __('Create item') }}</a></span>

	@if(count(sys_langs())>1)
	<section>
		<form action="{{ route('admin.faq') }}" method="get" class="form-inline">			
			<select name="search_lang_id" class="form-control @if($search_lang_id) is-valid @endif mr-2">
				<option selected="selected" value="">- {{ __('Any language') }} -</option>
				@foreach (sys_langs() as $sys_lang)
				<option @if($search_lang_id==$sys_lang->id) selected @endif value="{{ $sys_lang->id }}"> {{ $sys_lang->name }}</option>
				@endforeach
			</select>			
			<button class="btn btn-dark mr-2" type="submit"><i class="fas fa-check"></i> {{ __('Apply') }}</button>
			<a class="btn btn-light" href="{{ route('admin.faq') }}"><i class="fas fa-undo"></i></a>
		</form>
	</section>
	<div class="mb-3"></div>
	@endif			
	
	<div class="table-responsive-md">
		<table class="table table-bordered table-hover">

			<thead>
				<tr>
					<th width="40">#</th>
					<th>{{ __('Details') }}</th>
					@if(count(sys_langs())>1)
					<th width="160">{{ __('Language') }}</th>
					@endif
					<th width="150">{{ __('Actions') }}</th>
				</tr>
			</thead>

			<tbody>
				@foreach ($faqs as $faq)
				<tr @if ($faq->active==0) class="table-warning" @endif>

					<td>
						<h4>{{ $faq->position}}</h4>
					</td>
					<td>
						@if ($faq->active==0)<span class="pull-right"><button type="button" class="btn btn-warning btn-sm disabled">{{ __('Inactive') }}</button></span> @endif
						<h4>{{ $faq->title }}</h4>
					</td>

					@if(count(sys_langs())>1)
					<td>{{ $faq->lang_name ?? __('No language') }}</td>
					@endif

					<td>
						<a href="{{ route('admin.faq.show', ['id' => $faq->id]) }}" class="btn btn-primary btn-sm btn-block mb-2"><i class="fas fa-pen"></i> {{ __('Update') }}</a>

						<form method="POST" action="{{ route('admin.faq.show', ['id' => $faq->id]) }}">
							{{ csrf_field() }}
							{{ method_field('DELETE') }}
							<button type="submit" class="float-right btn btn-danger btn-sm btn-block delete-item-{{$faq->id}}"><i class="fas fa-trash-alt"></i> {{ __('Delete') }}</button>
						</form>
					
						<script>
							$('.delete-item-{{$faq->id}}').click(function(e){
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

    {{ $faqs->appends(['search_lang_id' => $search_lang_id])->links() }}

</div>
<!-- end card-body -->