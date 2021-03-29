<div class="card-header">
	<h3><i class="fas fa-cog"></i> {{ __('Email marketing config') }}</h3>
</div>
<!-- end card-header -->


<div class="card-body">

	@if ($message = Session::get('success'))
	<div class="alert alert-success">
		@if ($message=='updated') {{ __('Updated') }} @endif
	</div>
	@endif

	<div class="alert alert-success">
		{{ __('This module use Mailgun API. You MUST add your domain to Mailgun and create an API key to send bulk emails.') }} <a target="_blank" href="https://mailgun.com">{{ __('Create a Mailgun API key')}}</a>
	</div>

	<div class="row">
		<div class="col-md-4 col-12">

			<form method="post">
				@csrf

				<div class="form-group">
					<label>{{ __('Mailgun domain') }}</label>
					<div class="input-group">
						<input type="text" class="form-control" name="mailgun_domain" value="{{ $config->mailgun_domain ?? null }}">
					</div>					
				</div>

				<div class="form-group">
					<label>{{ __('Mailgun API key') }}</label>
					<div class="input-group">
						<input type="password" class="form-control" name="mailgun_api_key" value="{{ $config->mailgun_api_key ?? null }}">
					</div>					
				</div>

				<div class="form-group">
					<label>{{ __('Mailgun endpoint') }}</label>
					<div class="input-group">
						<select name="mailgun_endpoint" class="form-control" aria-describedby="departmentHelp">
							<option @if(($config->mailgun_endpoint ?? null) == 'api.mailgun.net') selected @endif value="api.mailgun.net">api.mailgun.net</option>
							<option @if(($config->mailgun_endpoint ?? null) == 'api.eu.mailgun.net') selected @endif value="api.eu.mailgun.net">api.eu.mailgun.net</option>							
						</select>
					</div>					
				</div>


				<button type="submit" class="btn btn-dark">{{ __('Update') }}</button>

			</form>
		</div>
	</div>

</div>
<!-- end card-body -->