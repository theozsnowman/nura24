<div class="card-header">	
	<h3><i class="far fa-envelope"></i> {{ __('Inbox') }} ({{ $count_inbox_unread ?? 0 }} {{ __('unread') }}, {{ $messages->total() ?? 0 }} {{ __('total') }})</h3>
</div>
<!-- end card-header -->

<div class="card-body">

	@if(! check_module('inbox'))
	<div class="alert alert-danger">
		{{ __('Warning. Contact page module is disabled. You can manege content, but module is disabled for visitors and registered users') }}. <a href="{{ route('admin.config.modules') }}">{{ __('Manage modules') }}</a>
	</div>
    @endif
    
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
		@if ($message=='replied') {{ __('Reply sent') }} @endif
		@if ($message=='deleted') {{ __('Deleted') }} @endif
		@if ($message=='updated') {{ __('Updated') }} @endif
	</div>
	@endif

	<section>
		@if(logged_user()->role == 'admin')
		<span class="float-right"><a href="{{ route('admin.config.contact') }}" class="btn btn-dark"><i class="fas fa-cog" aria-hidden="true"></i> {{ __('Contact page config') }}</a></span>
		@endif	

        <form action="{{ route('admin.inbox') }}" method="get" class="form-inline">
            <input type="text" name="search_terms" placeholder="{{ __('Search sender') }}" class="form-control mr-2 @if($search_terms) is-valid @endif" value="<?= $search_terms;?>" />
            <select name="search_status" class="form-control mr-2 @if($search_status) is-valid @endif">
                <option value="">- {{ __('Any status') }} -</option>
				<option @if ($search_status=='unread' ) selected="selected" @endif value="unread">{{ __('Only unread messages') }}</option>
				<option @if ($search_status=='read' ) selected="selected" @endif value="read">{{ __('Only read messages') }}</option>
            </select>

            <select class="form-control mr-2 @if($search_replied) is-valid @endif" name="search_replied">
                <option name="search_replied" selected="selected" value="">- {{ __('All messages') }} -</option>
                <option @if ($search_replied=='no' ) selected="selected" @endif value="no">{{ __('Only messages without reply') }}</option>
				<option @if ($search_replied=='yes' ) selected="selected" @endif value="yes">{{ __('Only messages with reply') }}</option>
			</select>
			
			<select class="form-control mr-2 @if($search_important) is-valid @endif" name="search_important">
                <option name="search_important" selected="selected" value="">- {{ __('All messages') }} -</option>
                <option @if ($search_important=='1' ) selected="selected" @endif value="1">{{ __('Only important messages') }}</option>
            </select>

            <button class="btn btn-dark mr-2" type="submit" /><i class="fas fa-check"></i> {{ __('Apply') }}</button>
            <a class="btn btn-light" href="{{ route('admin.inbox') }}"><i class="fas fa-undo"></i></a>
        </form>
    </section>
	<div class="mb-3"></div>
	
	<div class="table-responsive-md">
		<table class="table table-bordered table-hover">

			<thead>
				<tr>
					<th>{{ __('Details') }}</th>
					<th width="350">{{ __('Sender') }}</th>
					<th width="120">{{ __('Actions') }}</th>
				</tr>
			</thead>

			<tbody>

				@foreach ($messages as $message)
				<tr>

					<td>
						@if ($message->is_responded==1) <span class="pull-right ml-2"><button type="button" class="btn btn-success btn-sm"><i class="fas fa-reply"></i> {{ __('Replied') }}</button></span> @endif
						@if ($message->is_important==1) <span class="pull-right ml-2"><button type="button" class="btn btn-light btn-sm"><i class="fas fa-star text-danger"></i> {{ __('Important') }}</button></span> @endif

						@if ($message->is_read==0)
							<h4><font color="red">[{{ __('Unread') }}]</font>: <a class="text-bold" href="{{ route('admin.inbox.show', ['id'=>$message->id]) }}"><b>{{ $message->subject }}</b></a></h4>						
						@else <h4><a href="{{ route('admin.inbox.show', ['id'=>$message->id]) }}">{{ $message->subject }}</a></h4>
						@endif

						<div class="text-muted small">{{ date_locale($message->created_at, 'datetime') }}</div>

						<div class="text-muted">{{ substr($message->message, 0, 400) }}...</div>
					</td>

					<td>
						{{ $message->name }}<br />
						{{ $message->email }}<br />
						IP: {{ $message->ip }}
					</td>

					<td>

						<div class="d-flex">

							<a href="{{ route('admin.inbox.show', ['id'=>$message->id]) }}" class="btn btn-primary btn-sm mr-2"><i class="fas fa-search"></i></a>

							<form method="POST" action="{{ route('admin.inbox.show', ['id'=>$message->id]) }}">
								{{ csrf_field() }}
								{{ method_field('DELETE') }}
								<button type="submit" class="float-right btn btn-danger btn-sm btn-block delete-item-{{$message->id}}"><i class="fas fa-trash-alt"></i></button>
							</form>
						</div>

						<script>
							$('.delete-item-{{$message->id}}').click(function(e){
									e.preventDefault() // Don't post the form, unless confirmed
									if (confirm("{{ __('Are you sure to delete this item?') }} ")) {
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

	{{ $messages->links() }}


</div>
<!-- end card-body -->