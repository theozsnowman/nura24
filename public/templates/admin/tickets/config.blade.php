<div class="card-header">
	<h3><i class="fas fa-cog"></i> {{ __('Support tickets settings') }}</h3>
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
		@if ($message=='updated') {{ __('Updated') }} @endif
	</div>
	@endif

	<form method="post">
		@csrf

		<div class="form-row">

			<div class="form-group col-12">
				<label>{{ __('Announcement (will appear in clients tickets area)') }}</label>
				<textarea name="tickets_announcement" class="form-control" rows="3">{{ $config->tickets_announcement ?? null }}</textarea>					
			</div>

			<div class="form-group col-4">
				<label>{{ __('Allow clients to close own tickets') }}</label>
				<select name="tickets_client_can_close_ticket" class="form-control">
					<option @if(($config->tickets_client_can_close_ticket ?? null) == 'yes') selected @endif value="yes">{{ __('Yes') }}</option>
					<option @if(($config->tickets_client_can_close_ticket ?? null) == 'no') selected @endif value="no">{{ __('No') }}</option>
				</select>
			</div>

			<div class="form-group col-4">
				<label>{{ __('Allow clients to reopen closed tickets') }}</label>
				<select name="tickets_client_can_reopen_ticket" class="form-control">
					<option @if(($config->tickets_client_can_reopen_ticket ?? null) == 'yes') selected @endif value="yes">{{ __('Yes') }}</option>
					<option @if(($config->tickets_client_can_reopen_ticket ?? null) == 'no') selected @endif value="no">{{ __('No') }}</option>
				</select>
			</div>

			<div class="form-group col-4">
				<label>{{ __('Allow clients to upload files to tickets') }}</label>
				<select name="tickets_client_can_upload_files" class="form-control">
					<option @if(($config->tickets_client_can_upload_files ?? null) == 'yes') selected @endif value="yes">{{ __('Yes') }}</option>
					<option @if(($config->tickets_client_can_upload_files ?? null) == 'no') selected @endif value="no">{{ __('No') }}</option>
				</select>
			</div>
		</div>


		<div class="form-group">
			<button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
		</div>

	</form>

</div>
<!-- end card-body -->