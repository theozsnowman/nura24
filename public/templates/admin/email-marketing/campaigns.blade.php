<div class="card-header">
	<h3><i class="fas fa-envelope-open-text"></i> {{ __('Email campaigns') }} ({{ $campaigns->total() ?? 0 }})</h3>
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
		@if ($message=='created') {{ __('Created') }} @endif
		@if ($message=='updated') {{ __('Updated') }} @endif
		@if ($message=='deleted') {{ __('Deleted') }} @endif
		@if ($message=='sent') {{ __('Emails sent') }} @endif
	</div>
	@endif

	@if ($message = Session::get('error'))
	<div class="alert alert-danger">
		@if ($message=='duplicate') {{ __('Error. This campaign exists') }} @endif		
		@if ($message=='config') {{ __('Error. You must set Mailgun API first.') }} <a href="{{ route('admin.email.campaigns.config') }}">{{ __('Go to config') }}</a> @endif		
	</div>
	@endif

	<span class="float-right"><a class="btn btn-dark ml-2" href="{{ route('admin.email.campaigns.config') }}"><i class="fas fa-cog" aria-hidden="true"></i></a></span>

	<span class="float-right"><a class="btn btn-danger ml-2" href="{{ route('admin.email.black-list') }}"><i class="fas fa-times" aria-hidden="true"></i> {{ __('Black list') }}</a></span>

	<span class="float-right"><a class="btn btn-success ml-2" href="{{ route('admin.email.lists') }}"><i class="fas fa-envelope" aria-hidden="true"></i> {{ __('Mailing lists') }}</a></span>

	<span class="float-right mb-3"><a class="btn btn-primary" href="{{ route('admin.email.campaigns.create') }}"><i class="fas fa-plus-square" aria-hidden="true"></i> {{ __('Create campaign') }}</a></span>

	<div class="table-responsive-md">
		<table class="table table-bordered table-hover">
			<thead>
				<tr>
					<th width="70">ID</th>
					<th>{{ __('Details') }}</th>				
					<th width="180">{{ __('Recipients') }}</th>
					<th width="200">{{ __('Actions') }}</th>
				</tr>
			</thead>

			<tbody>
				@foreach ($campaigns as $campaign)
				<tr>

					<td>
						{{ $campaign->id}}
					</td>
					<td>
						@if (! $campaign->sent_at)
						<span class="pull-right"><button type="button" class="btn btn-warning btn-sm disabled">{{ __('Mails not sent') }}</button></span>
						@else 
						<span class="pull-right"><button type="button" class="btn btn-success btn-sm disabled">{{ __('Mails sent') }}</button></span>
						@endif					

						<h4>{{ $campaign->title }}</h4>
						{{ __('Subject') }}: <b>{{ $campaign->subject }}</b>
					
						<div class="mb-2"></div>

						@if($campaign->description)
						{!! nl2br($campaign->description) !!}
						<br>						
						@endif

						<small class='text-muted'>
						{{ __('Created') }}: {{ date_locale($campaign->created_at, 'datetime') }} 
						@if($campaign->sent_at)<br>{{ __('Emails sent at') }}: {{ date_locale($campaign->sent_at, 'datetime') }}@endif
						</small>
						<br>					
					</td>
				
					
					<td>
						<h4><a href="{{ route('admin.email.campaigns.recipients', ['id' => $campaign->id]) }}">{{ $campaign->count_recipients }} {{ __('recipients') }}</a></h4>
					</td>
					

					<td>		
						<a href="{{ route('admin.email.campaigns.recipients', ['id' => $campaign->id]) }}" class="btn btn-dark btn-block btn-sm mb-2"><i class="far fa-envelope"></i> {{ __('Manage recipients') }}</a>

						<a href="{{ route('admin.email.campaigns.send', ['id' => $campaign->id]) }}" class="btn btn-success btn-block btn-sm mb-2"><i class="fas fa-paper-plane"></i> {{ __('Send emails') }}</a>

						<a href="{{ route('admin.email.campaigns.show', ['id' => $campaign->id]) }}"  class="btn btn-primary btn-block btn-sm mb-2"><i class="fas fa-pen"></i> {{ __('Update campaign') }}</a>

						<form method="POST" action="{{ route('admin.email.campaigns.show', ['id' => $campaign->id]) }}">
							{{ csrf_field() }}
							{{ method_field('DELETE') }}
							<button type="submit" class="float-right btn btn-danger btn-sm btn-block delete-item-{{$campaign->id}}"><i class="fas fa-trash-alt"></i> {{ __('Delete campaign') }}</button>
						</form>

						<script>
							$('.delete-item-{{$campaign->id}}').click(function(e){
									e.preventDefault() // Don't post the form, unless confirmed
									if (confirm('Are you sure to delete this campaign?')) {
										$(e.target).closest('form').submit() // Post the surrounding form
									}
								});
						</script>
					</td>
				</tr>
				@endforeach

			</tbody>
		</table>
	</div>

	{{ $campaigns->links() }}

</div>
<!-- end card-body -->