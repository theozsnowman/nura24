<div class="card-header">
	<h3><i class="fas fa-flag"></i> {{ __('Translations') }}</h3>
</div>
<!-- end card-header -->

<div class="card-body">

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

	<div class="alert alert-info">
		{{ __('Info: You cau automatic scan template files to automatic add all translate keys into database') }}
	</div>

	<span class="pull-right mb-0">
		<button class="btn btn-primary" data-toggle="modal" data-target="#modal-create-translate-key"><i class="fas fa-plus" aria-hidden="true"></i> {{ __('Add new translate key') }}</button>
		@include('admin.core.modals.create-translate-key')
	</span>

	<div>
		
		<form class="form-inline mb-3 pull-left" method="post" action="{{ route('admin.translates.scan_template') }}">
			@csrf
			<label>{{ __('Frontend template') }}</label>
			<select name="template" class="form-control mr-2 ml-2" required>
				<option value=''>- {{ __('Select') }} -</option>
				@foreach ($templates as $template)
				<option value="{{ $template }}">{{ basename($template) }}</option>
				@endforeach
			</select>
			<input type="hidden" name="area" value="frontend">
			<button type="submit" class="btn btn-dark">{{ __('Scan files') }}</button>
		</form>

		<form class="form-inline mb-3 pull-left" method="post" action="{{ route('admin.translates.scan_template') }}">
			@csrf
			<label class="mr-2 ml-2">{{ __('Users area') }}</label>
			<select name="template" class="form-control mr-2" required>
				<option value=''>- {{ __('Select') }} -</option>
				<option value="templates/user">templates/user</option>
				<option value="templates/auth">templates/auth</option>
			</select>
			<input type="hidden" name="area" value="users">
			<button type="submit" class="btn btn-dark">{{ __('Scan files') }}</button>
		</form>

		<form class="form-inline mb-3" method="post" action="{{ route('admin.translates.scan_template') }}">
			@csrf
			<label class="mr-2 ml-2">{{ __('Admin and internal area') }}</label>
			<select name="template" class="form-control mr-2" required>
				<option value="templates/admin">templates/admin</option>
			</select>
			<input type="hidden" name="area" value="admin">
			<button type="submit" class="btn btn-dark">{{ __('Scan files') }}</button>
		</form>

	</div>

	<h4>{{ $count_keys }} {{ __('translate keys') }}</h4>

	<div class="table-responsive">
		<table class="table table-bordered table-hover">
			<thead>
				<tr>
					<th>{{ __('Language') }}</th>
					<th width="260">{{ __('Statistics') }}</th>
					<th width="200">{{ __('Translate') }}</th>
				</tr>
			</thead>
			<tbody>

				@foreach ($langs as $lang)
				<tr @if ($lang->status != 'active') class="table-warning" @endif>

					<td>
						@if ($lang->status == 'inactive') <span class="pull-right">&nbsp;<button type="button" class="btn btn-warning btn-sm disabled">{{ __('Inactive') }}</button> </span> @endif
						@if ($lang->status == 'disabled') <span class="pull-right">&nbsp;<button type="button" class="btn btn-danger btn-sm disabled">{{ __('Disabled') }}</button> </span> @endif
						@if ($lang->is_default==1) <span class="pull-right">&nbsp;<button type="button" class="btn btn-info btn-sm disabled">{{ __('Default Language') }}</button> </span> @endif
						<h4>{{ $lang->name }}</h4>
						{{ __('Code') }}: <b>{{ $lang->code }}</b>
					</td>

					<td>
						<h5>
							@if($count_keys - $lang->count_translated_keys > 0)
							<span class="text-danger"> {{ $count_keys - $lang->count_translated_keys }} {{ __('untranslated keys') }}</span>
							@else
							<span class="text-success"> {{ __('translation completed') }}</span>
							@endif
						</h5>
					</td>

					<td>
						<a class="btn btn-dark btn-block" href="{{ route('admin.translate_lang', ['id' => $lang->id]) }}">{{ __('Manage translations') }}</a>
					</td>

				</tr>
				@endforeach

			</tbody>
		</table>
	</div>

</div>
<!-- end card-body -->