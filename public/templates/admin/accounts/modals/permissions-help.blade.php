<div class="modal fade custom-modal" tabindex="-1" role="dialog" aria-labelledby="permissionsHelp" aria-hidden="true" id="permissionsHelp">
	<div class="modal-dialog">
		<div class="modal-content">


			<div class="modal-header">
				<h5 class="modal-title" id="permissionsHelp">{{ __('Create account tag') }}</h5>
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">{{ __('Close') }}</span></button>
			</div>

			<div class="modal-body">

				@foreach($modules_permissions as $module_permissions)
				<h4 class="text-info">{{ $module_permissions->module_label }} {{ __('permissions') }}</h4>

				@foreach($module_permissions->permissions as $permission)
				<b>{{ $permission->label }}</b>: {{ $permission->description }}
				<div class="mb-2"></div>
				@endforeach

				<hr>

				@endforeach

			</div>

			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			</div>


		</div>
	</div>
</div>