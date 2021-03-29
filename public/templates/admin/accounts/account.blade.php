<div class="card-header">
	<h3><i class="far fa-user"></i> {{ $account->name}} ({{ $account->email}})</h3>
</div>
<!-- end card-header -->

<div class="card-body">

	@include('admin.accounts.layouts.menu-account')
	<div class="mb-3"></div>

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
		@if ($message=='updated') {{ __('Updated') }}@endif
	</div>
	@endif
	
	<div class="row mb-3">
		<div class="col-12">
			@if ($account->avatar)
			<span class="float-left mr-2"><img style="max-height:120px; width:auto;" src="{{ asset('uploads/'.$account->avatar) }}" /></span>
			@endif
			{{ __('ID') }}: {{ strtoupper($account->id) }} <br>
			{{ __('Code') }}: {{ strtoupper($account->code) ?? null}} <br>
			{{ __('Registered') }}: {{ date_locale($account->created_at, 'datetime') }} <br>
			{{ __('Last activity') }}: @if($account->last_activity){{ date_locale($account->last_activity, 'datetime') }}@else {{ __('never') }}@endif
		</div>
	</div>	

	<form action="{{ route('admin.accounts.show', ['id' => $account->id]) }}" method="post" enctype="multipart/form-data">
		@csrf
		@method('PUT')

		<div class="row">

			<div class="col-lg-12">
				<div class="form-group">
					<label>{{ __('Full name') }}</label>
					<input class="form-control" name="name" type="text" required value="{{ $account->name }}" />
				</div>
			</div>

			<div class="col-lg-6">
				<div class="form-group">
					<label>{{ __('Email') }}</label>
					<input class="form-control" name="email" type="email" required value="{{ $account->email }}" />
				</div>
			</div>

			<div class="col-lg-6">
				<div class="form-group">
					<label>{{ __('Role') }}</label>
					<select name="role_id" class="form-control" required>
						<option value="">- {{ __('select') }} -</option>
						@foreach ($roles as $role)
						<option @if ($account->role_id==$role->id) selected="selected" @endif value="{{ $role->id }}">
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
							{{ $account->role }}
							@endswitch
						</option>
						@endforeach
					</select>
				</div>
			</div>

			<div class="col-lg-6">
				<div class="form-group">
					<label>{{ __('Email verified') }}</label>
					<select name="email_verified" class="form-control">
						<option @if ($account->email_verified_at) selected="selected" @endif value="1">{{ __('Yes') }}</option>
						<option @if (! $account->email_verified_at) selected="selected" @endif value="0">{{ __('No') }}</option>
					</select>
				</div>
			</div>

			<div class="col-lg-6">
				<div class="form-group">
					<label>{{ __('Active') }}</label>
					<select name="active" class="form-control">
						<option @if ($account->active==1) selected="selected" @endif value="1">{{ __('Yes') }}</option>
						<option @if ($account->active==0) selected="selected" @endif value="0">{{ __('No') }}</option>
					</select>
				</div>
			</div>

			<div class="col-lg-6">
				<div class="form-group">
					<label>{{ __('Change password') }} ({{ __('optional') }})</label>
					<input class="form-control" name="password" type="password" />
				</div>
			</div>

			<div class="col-lg-6">
				<div class="form-group">
					<label>{{ __('Change avatar image') }} ({{ __('optional') }}):</label>
					<div class="custom-file">
						<input type="file" class="custom-file-input" id="validatedCustomFile" name="avatar">
						<label class="custom-file-label" for="validatedCustomFile">{{ __('Choose file') }}...</label>
					</div>
				</div>
			</div>

		</div>

		<hr>
		@if(check_access('accounts', 'manager'))
		<button type="submit" class="btn btn-primary">{{ __('Update account') }}</button>
		@endif

	</form>

</div>
<!-- end card-body -->