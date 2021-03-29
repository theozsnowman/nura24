<div class="card-header">
	<h3><i class="fas fa-flag"></i> {{ __('Translates for') }} {{ $lang->name }} ({{ $lang_keys->total() }} {{ __('keys total') }}, {{ $translated_keys }} {{ __('keys translated') }})</h3>
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
		@if ($message=='regenerated') {{ __('Translation file updated') }} @endif
	</div>
	@endif

	@if ($message = Session::get('error'))
	<div class="alert alert-danger">
		@if ($message=='error_file') {{ __('Error. Choose file') }} @endif
		@if ($message=='duplicate') {{ __('Error. This language key exist') }} @endif
	</div>
	@endif

	<span class="pull-right mb-3">
		<a class="btn btn-danger mr-3" href="{{ route('admin.translates.regenerate_lang_file', ['locale' => $locale, 'lang_id' => $lang->id]) }}">{{ __('Update translations file ') }} - {{ $lang->name }}</a>
		<button class="btn btn-primary" data-toggle="modal" data-target="#modal-create-translate-key"><i class="fas fa-plus" aria-hidden="true"></i> {{ __('Add new translate key') }}</button>
		@include('admin.core.modals.create-translate-key')
	</span>

	<section>
		<form action="{{ route('admin.translate_lang', ['id' => $lang->id]) }}" method="get" class="form-inline">
			<input type="text" name="search_terms" placeholder="{{ __('Search key') }}" class="form-control @if($search_terms) is-valid @endif mr-2" value="{{ $search_terms ?? null }}" />
			<input type="hidden" name="id" value="{{ $lang->id }}">
			<button class="btn btn-dark mr-2" type="submit"><i class="fas fa-check"></i> {{ __('Apply') }}</button>
			<a class="btn btn-light" href="{{ route('admin.translate_lang', ['id' => $lang->id]) }}"><i class="fas fa-undo"></i></a>
		</form>
	</section>
	<div class="mb-3"></div>

	<div class="table-responsive">
		<table class="table table-bordered table-hover">
			<thead>
				<tr>
					<th width="450">{{ __('Language key') }}</th>
					<th width="150">{{ __('Area') }}</th>
					<th>{{ __('Translates') }} ({{ $lang->name }})</th>
				</tr>
			</thead>
			<tbody>

				@foreach ($lang_keys as $lang_key)
				<tr>

					<td>
						<h4>{{ $lang_key->lang_key }}</h4>

						<div class="mb-4"></div>

						<div class="d-flex">
							<button data-toggle="modal" data-target="#modal-update-translate-key-{{ $lang_key->id }}" class="btn btn-primary btn-sm  mr-2"><i class="fas fa-edit" aria-hidden="true"></i></button>
							@include('admin.core.modals.update-translate-key')

							<form method="POST" action="{{ route('admin.translates.delete_key', ['key_id' => $lang_key->id, 'lang_id' => $lang->id]) }}">
								@csrf
								<button type="submit" class="float-right btn btn-danger btn-sm delete-item-{{$lang_key->id}}"><i class="fas fa-trash-alt"></i></button>
							</form>
							<script>
								$('.delete-item-{{$lang_key->id}}').click(function(e){
											e.preventDefault() // Don't post the form, unless confirmed
											if (confirm("{{ __('Are you sure to delete this item?') }}'")) {
												$(e.target).closest('form').submit() // Post the surrounding form
											}
										});
							</script>
						</div>
					</td>

					<td>
						<h5>{{ $lang_key->area }}</h5>
					</td>

					<td>
						<form action="{{ route('admin.translates.update_translate') }}" method="POST">
							@csrf
							<textarea class="form-control" name="translate" rows="2">{{ $lang_key->translate ?? null }}</textarea>
							<input type="hidden" name="lang_id" value="{{ $lang->id }}">
							<input type="hidden" name="key_id" value="{{ $lang_key->id }}">
							<button type="submit" class="btn btn-primary btn-sm btn-dark mt-2">{{ __('Update') }}</button>
						</form>
						<hr>

					</td>

				</tr>
				@endforeach

			</tbody>
		</table>
	</div>

	{{ $lang_keys->appends(['id' => $lang_id, 'search_terms' => $search_terms])->links() }}

</div>
<!-- end card-body -->