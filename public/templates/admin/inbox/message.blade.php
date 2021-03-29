<div class="card-header">
	<span class="pull-right"><a href="{{ route('admin.inbox') }}" class="btn btn-info"><i class="fas fa-envelope" aria-hidden="true"></i> {{ __('Inbox') }}</a></span>
	<h3><i class="far fa-envelope"></i> {{ __('Details') }}</h3>
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

	<div class="row">
		<div class="col-lg-9 col-xl-9">
			<h4>{{ $message->subject }}</h4>
			<p>{!! nl2br($message->message) !!}</p>
			<hr />

			<div class="mb-3"></div>

			@if ($replies)
			<h4>{{ $replies->total() }} {{ __('replies')}}:</h4>
			@endif

			@foreach ($replies as $reply)

			{!! $reply->message !!}
			<div class="text-muted text-small mt-2">
				{{ __('Sender') }} <b>{{ $reply->author_name }}</b> - {{ date_locale($reply->created_at, 'datetime') }}
			</div>

			<hr />
			@endforeach

			<form action="{{ route('admin.inbox.reply', ['id'=>$message->id]) }}" method="post">
				@csrf
				<div class="row">
					<div class="col-lg-12">
						<div class="form-group">
							<h4>{{ __('Send reply') }}</h4>

							<div class="alert alert-info" role="alert">
								<i class="fas fa-exclamation-circle"></i> {{ __('You will send reply to') }} <b>{{ $message->email }}</b>.
								{{ __('If destinatar will reply, you will get destinatar replies in your email') }}: <b>{{ $config->site_email }}</b>
							</div>

							<textarea class="form-control" name="reply" rows="6" required></textarea>
						</div>
					</div>

					<div class="col-lg-12">
						<input type="hidden" name="msg_id" value="{{ $message->id }}" />
						<button type="submit" class="btn btn-primary">{{ __('Send reply') }}</button>
					</div>
				</div>
			</form>
		</div>


		<div class="col-lg-3 col-xl-3 border-left">
			<h4>{{ __('Sender details') }}</h4>

			<b>{{ __('Name') }}</b> {{ $message->name }}
			<br />
			<b>{{ __('Email') }}</b> {{ $message->email }}
			<br />
			<b>{{ __('Sent at') }}</b>: {{ date_locale($message->created_at, 'datetime') }}
			<br />
			<b>IP: </b>: {{ $message->ip }}

			<hr>
			@if($message->is_important==0)
			<a href="{{ route('admin.inbox.important', ['id'=>$message->id, 'action'=>'set']) }}" class="btn btn-success"><i class="fas fa-star"></i> {{ __('Flag as important') }}</a>
			@else
			<a href="{{ route('admin.inbox.important', ['id'=>$message->id, 'action'=>'unset']) }}" class="btn btn-success"><i class="fas fa-star"></i> {{ __('Flag as normal') }}</a>
			@endif			

		</div>
	</div>

</div>
<!-- end card-body -->