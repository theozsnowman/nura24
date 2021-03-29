<div class="card-header">

	@include('admin.core.layouts.menu-config')

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
		@if ($message=='test_email_ok') {{ __('Test email sent. Please check your email address') }} @endif
	</div>
	@endif

	<form method="post">
		@csrf

		<div class="form-row">
			<div class="form-group col-md-3">
				<label>{{ __('Site email address') }} (From: email)</label>
				<input class="form-control" name="site_email" type="text" value="{{ $config->site_email ?? NULL }}">
			</div>

			<div class="form-group col-md-3">
				<label>{{ __('Email name') }} (From: name)</label>
				<input type="text" class="form-control" name="site_email_name" value="{{ $config->site_email_name ?? NULL }}">
			</div>

			<div class="form-group col-md-3">
				<label>{{ __('Mail sending option') }}</label>
				<select name="mail_sending_option" class="form-control">
					<option @if ($config->mail_sending_option ?? null == 'smtp') selected @endif value="smtp">{{ __('SMTP mailer (recomended)') }}</option>
					<option @if ($config->mail_sending_option ?? null == 'php') selected @endif value="php">{{ __('PHP mailer (NOT recomended)') }}</option>
				</select>
			</div>

			<script>
				$(document).ready(function() {
							@if (!empty($config->mail_sending_option)) @if ($config->mail_sending_option=='php')
								$('#smtp_server').attr('readonly','readonly'); 
								$('#smtp_user').attr('readonly','readonly'); 
								$('#smtp_password').attr('readonly','readonly'); 
								$('#smtp_port').attr('readonly','readonly'); 
								$('#smtp_encryption').attr('readonly','readonly'); 
								$('#debug').attr('disabled','disabled'); 
							@endif @endif
							
							@if (!empty($config->mail_sending_option)) @if ($config->mail_sending_option=='smtp')				
								$('#sendmail_path').attr('readonly','readonly'); 
							@endif @endif
													
							
							$('select[name="mail_sending_option"]').on('change',function(){
							var selector = $(this).val();
								if(selector == "smtp")
								{           
									$('#smtp_server').removeAttr('readonly'); 
									$('#smtp_user').removeAttr('readonly');									
									$('#smtp_password').removeAttr('readonly');
									$('#smtp_port').removeAttr('readonly');
									$('#smtp_encryption').removeAttr('readonly');
									$('#debug').removeAttr('disabled');
								 }
								 else
								 {
									$('#smtp_server').attr('readonly','readonly'); 
									$('#smtp_user').attr('readonly','readonly'); 
									$('#smtp_password').attr('readonly','readonly'); 
									$('#smtp_port').attr('readonly','readonly'); 
									$('#smtp_encryption').attr('readonly','readonly'); 
									$('#debug').attr('disabled','disabled'); 
								 }  
																
							  });
							});
			</script>
		</div>

		<div class="form-row">
			<div class="form-group col-md-2">
				<label>SMTP server</label>
				<input type="text" class="form-control" name="smtp_server" id="smtp_server" value="{{ $config->smtp_server ?? NULL }}">
			</div>

			<div class="form-group col-md-2">
				<label>SMTP user</label>
				<input type="text" class="form-control" name="smtp_user" id="smtp_user" value="{{ $config->smtp_user ?? NULL }}">
			</div>

			<div class="form-group col-md-2">
				<label>SMTP password</label>
				<input type="password" class="form-control" name="smtp_password" id="smtp_password" value="{{ $config->smtp_password ?? NULL }}">
			</div>

			<div class="form-group col-md-2">
				<label>SMTP port</label>
				<input type="text" class="form-control" name="smtp_port" id="smtp_port" value="{{ $config->smtp_port ?? NULL }}">
			</div>

			<div class="form-group col-md-2">
				<label>SMTP encryption</label>
				<select name="smtp_encryption" class="form-control" id="smtp_encryption">
					<option @if ($config->smtp_encryption ?? null == 'tls') selected @endif value="tls">TLS</option>
					<option @if ($config->smtp_encryption ?? null == 'ssl') selected @endif value="ssl">SSL</option>
				</select>
			</div>
		</div>

		<hr />

		<div class="form-row">
			<div class="form-group col-md-12">
				<label>{{ __('Global signature') }}</label>
				<textarea class="form-control editor" name="mail_global_signature" id="mail_global_signature">{!! $config->mail_global_signature ?? NULL !!}</textarea>
			</div>

			<div class="form-group col-md-12">
				<label>{{ __('Global CSS style code') }}</label>
				<textarea rows="14" class="form-control" name="mail_global_css" id="mail_global_css">{!! $config->mail_global_css ?? NULL !!}</textarea>
			</div>
		</div>


		<div class="form-group">
			<button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
		</div>

	</form>

</div>
<!-- end card-body -->

<div class="card-header">
	<h3><i class="far fa-envelope"></i> {{ __('Test email settings') }}</h3>
	{{ __('Send a test email using your settings') }}
</div>
<!-- end card-header -->

<div class="card-body">
	<form action="{{ route('admin.send_test_email') }}" method="post" target="_blank">
		@csrf
		<div class="form-group form-inline">
			<input type="email" class="form-control" name="test_email" placeholder="{{ __('Input email') }}" required>
			<div class="mr-3"></div>
			<input type="checkbox" class="form-check-input" id="debug" name="debug">
			<label class="form-check-label" for="debug">{{ __('Show debug info') }}</label>
			<div class="mr-3"></div>
			<button type="submit" class="btn btn-primary">{{ __('Send test email') }}</button>
		</div>
	</form>
</div>