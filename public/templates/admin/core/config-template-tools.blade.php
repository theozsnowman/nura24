<div class="card-header">
	@include('admin.core.layouts.menu-template')
</div>
<!-- end card-header -->

<div class="card-body">

	@if ($message = Session::get('success'))
	<div class="alert alert-success">
		@if ($message=='updated') {{ __('Updated') }} @endif
	</div>
	@endif

	<div class="alert alert-info" role="alert">
		<b>{{ __('This template codes will be inserted in active frontend template') }}.</b><br>
		{{ __('Head code is inserted inside head area in your template ') }}.<br>
		{{ __('Footer code is inserted at the end section in your template code') }}.
		<hr>
		{{ __('You can add global css / javascript files, or even html / javascript code') }}.
	</div>

	<form method="post">
		@csrf

		<div class="form-row">

			<div class="form-group col-12">
				<label>{{ __('Code added in template head') }}</label>
				<textarea class="form-control" name="template_global_head_code" rows="10">{{ $config->template_global_head_code ?? NULL }}</textarea>
			</div>

			<div class="form-group col-12">
				<label>{{ __('Code added in template footer') }}</label>
				<textarea class="form-control" name="template_global_footer_code" rows="10">{{ $config->template_global_footer_code ?? NULL }}</textarea>
			</div>

		</div>

		<div class="form-group">
			<button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
		</div>

	</form>

</div>
<!-- end card-body -->