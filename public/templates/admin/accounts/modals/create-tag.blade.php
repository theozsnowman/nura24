<div class="modal fade custom-modal" tabindex="-1" role="dialog" aria-labelledby="create-tag" aria-hidden="true" id="create-tag">
	<div class="modal-dialog">
		<div class="modal-content">

			<form method="post">
				@csrf

				<div class="modal-header">
					<h5 class="modal-title" id="create-tag">{{ __('Create account tag') }}</h5>
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">{{ __('Close') }}</span></button>
				</div>

				<div class="modal-body">

					<div class="row">
						<div class="col-lg-12">
							<div class="form-group">
								<label>{{ __('Tag') }}</label>
								<input class="form-control" name="tag" type="text" required />
							</div>
						</div>

						<div class="col-lg-6">
							<div class="form-group">
								<label>{{ __('Color') }}</label>
								<input type="color" id="color" class="form-control" name="color" autocomplete="off" value="#b7b7b7">
							</div>
						</div>
								
						<div class="col-lg-6">
							<div class="form-group">
								<label>{{ __('Role') }}</label>
								<select name="role_id" class="form-control" required>
									<option value="">- {{ __('select') }} -</option>
									@foreach ($active_roles as $role)
									<option value="{{ $role->id }}">
										@switch($role->role)
										@case('admin')
										{{ __('Administrator') }}
										@break
										
										@case('user')
										{{ __('Registered user') }}
										@break

										@case('internal')
										{{ __('Internal') }}
										@break

										@case('vendor')
										{{ __('Vendor') }}
										@break

										@default
										{{ $user->role }}
										@endswitch
									</option>
									@endforeach
								</select>
							</div>
						</div>

					</div>
					
				</div>

				<div class="modal-footer">
					<button type="submit" class="btn btn-primary">{{ __('Create account tag') }}</button>
				</div>

			</form>

		</div>
	</div>
</div>