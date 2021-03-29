<div class="modal fade custom-modal" tabindex="-1" role="dialog" aria-labelledby="update-account-{{ $user->id }}" aria-hidden="true" id="update-account-{{ $user->id }}">
	<div class="modal-dialog">
		<div class="modal-content">

			<form action="{{ route('admin.accounts.show', ['id' => $user->id]) }}" method="post" enctype="multipart/form-data">
				@csrf
				@method('PUT')

				<div class="modal-header">
					<h5 class="modal-title" id="update-account-{{ $user->id }}">{{ __('Update account') }}</h5>
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">{{ __('Close') }}</span></button>
				</div>

				<div class="modal-body">

					<div class="row">
						<div class="col-lg-12">
							<div class="form-group">
								<label>{{ __('Full name') }}</label>
								<input class="form-control" name="name" type="text" required value="{{ $user->name }}" />
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-lg-6">
							<div class="form-group">
								<label>{{ __('Email') }}</label>
								<input class="form-control" name="email" type="email" required value="{{ $user->email }}" />
							</div>
						</div>

						<div class="col-lg-6">
							<div class="form-group">
								<label>{{ __('Change password') }} ({{ __('optional') }})</label>
								<input class="form-control" name="password" type="text" />
							</div>
						</div>
					</div>

					<div class="row">

						<div class="col-lg-6">
							<div class="form-group">
								<label>{{ __('Role') }}</label>
								<select name="role_id" class="form-control" required>
									<option value="">- {{ __('select') }} -</option>
									@foreach ($roles as $role)
									<option @if ($user->role_id==$role->id) selected="selected" @endif value="{{ $role->id }}">
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

					<div class="row">

						<div class="col-lg-6">
							<div class="form-group">
								<label>{{ __('Email verified') }}</label>
								<select name="email_verified" class="form-control">
									<option @if ($user->email_verified_at) selected="selected" @endif value="1">{{ __('Yes') }}</option>
									<option @if (!$user->email_verified_at) selected="selected" @endif value="0">{{ __('No') }}</option>
								</select>
							</div>
						</div>

						<div class="col-lg-6">
							<div class="form-group">
								<label>{{ __('Active') }}</label>
								<select name="active" class="form-control">
									<option @if ($user->active==1) selected="selected" @endif value="1">{{ __('Yes') }}</option>
									<option @if ($user->active==0) selected="selected" @endif value="0">{{ __('No') }}</option>
								</select>
							</div>
						</div>

					</div>

					<div class="form-group">
						<label>{{ __('Change avatar image') }} ({{ __('optional') }}):</label>
						<div class="custom-file">
							<input type="file" class="custom-file-input" id="validatedCustomFile" name="avatar">
							<label class="custom-file-label" for="validatedCustomFile">{{ __('Choose file') }}...</label>
						</div>
					</div>

				</div>

				<div class="modal-footer">
					<button type="submit" class="btn btn-primary">{{ __('Update account') }}</button>
				</div>

			</form>

		</div>
	</div>
</div>