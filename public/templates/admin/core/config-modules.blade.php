<div class="card-header">
	<h3><i class="fas fa-cog"></i> {{ __('Modules') }}</h3>
</div>
<!-- end card-header -->


<div class="card-body">

	<div class="alert alert-info">
		{{ __('Active modules - Module is enabled for visitors and registered users') }}.
		<br>
		{{ __('Inactive modules - Administrators and staff (with module permission) have access to module content, but module is disabled for visitors and registered users') }}.
		<br>
		{{ __('Disabled modules - Module is disabled and it is not displayed in administration area') }}.
	</div>


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

	<div class="table-responsive">
		<table class="table table-bordered table-hover">
			<thead>
				<tr>
					<th>{{ __('Module') }}</th>
					<th width="160">{{ __('Status') }}</th>
					<th width="160">{{ __('Update') }}</th>
				</tr>
			</thead>
			<tbody>

				@foreach ($modules as $module)
				<tr>
					<td>
						<h4>{{ $module->label }}</h4>
						<b>{{ $module->module }}</b>
						@if($module->description)<div class="text-muted mt-2">{{ $module->description }}</div>@endif
					</td>

					<td>
						@if ($module->status=='disabled') <button type="button" class="btn btn-danger btn-sm btn-block">{{ __('Disabled') }}</button> @endif
						@if ($module->status=='inactive') <button type="button" class="btn btn-warning btn-sm btn-block">{{ __('Inactive') }}</button> @endif
						@if ($module->status=='active') <button type="button" class="btn btn-success btn-sm btn-block">{{ __('Active') }}</button> @endif
					</td>

					<td>
						<div class="d-flex">
							<button data-toggle="modal" data-target="#update-module-{{ $module->id }}" class="btn btn-dark btn-sm btn-block"><i class="fas fa-edit" aria-hidden="true"></i> {{ __('Update') }}</button>
							@include('admin.core.modals.update-module')
						</div>
					</td>
				</tr>
				@endforeach

			</tbody>
		</table>
	</div>


</div>
<!-- end card-body -->