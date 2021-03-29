<div class="card-header">
    <h3><i class="fas fa-ticket-alt"></i> {{ __('Support tickets') }} ({{ $tickets->total() ?? 0 }})</h3>
</div>
<!-- end card-header -->

<div class="card-body">

    @if(! check_module('tickets'))
	<div class="alert alert-danger">
		{{ __('Warning. Support tickets module is disabled. You can manege content, but module is disabled for visitors and registered users') }}. <a href="{{ route('admin.config.modules') }}">{{ __('Manage modules') }}</a>
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
        @if ($message=='created') {{ __('Created') }} @endif
        @if ($message=='updated') {{ __('Updated') }} @endif
        @if ($message=='deleted') {{ __('Deleted') }} @endif
        @if ($message=='closed') {{ __('Ticket closed') }} @endif
    </div>
    @endif

    <span class="pull-right mb-3">
        <a class="btn btn-dark" href="{{ route('admin.tickets.config') }}"><i class="fas fa-cog"></i> {{ __('Settings') }}</a>
        <a class="btn btn-dark" href="{{ route('admin.tickets.departments') }}"><i class="fas fa-cog"></i> {{ __('Departments') }}</a>
    </span>

    <div class="clearfix"></div>

    @if($count_new_tickets>0 or $count_waiting_operator_tickets>0)
    <div class="alert alert-warning">
        @if($count_new_tickets>0) <b><a href="{{ route('admin.tickets', ['search_status' => 'new']) }}">{{ $count_new_tickets }} {{ __('new tickets') }}</a></b> <br> @endif
        @if($count_waiting_operator_tickets>0) <b><a href="{{ route('admin.tickets', ['search_status' => 'waiting_operator']) }}">{{ $count_waiting_operator_tickets }} {{ __('tickets waiting for response') }}</a></b>@endif
    </div>
    @endif

    <section>
        <form action="{{ route('admin.tickets') }}" method="get" class="form-inline">

            <input type="text" name="search_terms" placeholder="{{ __('Search ticket') }}" class="form-control mr-2" value="{{ $search_terms ?? '' }}" />

            <select name="search_status" class="form-control mr-2">
                <option selected="selected" value="">- {{ __('All tickets') }} -</option>
                <option @if($search_status=='new' ) selected @endif value="new"> {{ __('New tickets') }}</option>
                <option @if($search_status=='waiting_operator' ) selected @endif value="waiting_operator"> {{ __('Answered by client') }}</option>
                <option @if($search_status=='waiting_client' ) selected @endif value="waiting_client"> {{ __('Waiting client response') }}</option>
                <option @if($search_status=='closed' ) selected @endif value="closed"> {{ __('Closed tickets') }}</option>
            </select>

            <select name="search_priority" class="form-control mr-2">
                <option selected="selected" value="">- {{ __('Any priority') }} -</option>
                <option @if($search_priority=='0' ) selected @endif value="0"> {{ __('Normal') }}</option>
                <option @if($search_priority=='1' ) selected @endif value="1"> {{ __('Important') }}</option>
                <option @if($search_priority=='2' ) selected @endif value="2"> {{ __('Urgent') }}</option>
            </select>

            <select name="search_department_id" class="form-control mr-2">
                <option selected="selected" value="">- {{ __('All departments') }} -</option>
                @foreach($departments as $department)
                <option @if($search_department_id==$department->id) selected @endif value="{{ $department->id }}"> {{ $department->title }}</option>
                @endforeach
            </select>

            <button class="btn btn-dark mr-2" type="submit"><i class="fas fa-check"></i> {{ __('Apply') }}</button>
            <a class="btn btn-light" href="{{ route('admin.tickets') }}"><i class="fas fa-undo"></i></a>
        </form>
    </section>
    <div class="mb-3"></div>

    <div class="table-responsive-md">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>{{ __('Details') }}</th>
                    <th width="280">{{ __('Client') }}</th>
                    <th width="220">{{ __('Department') }}</th>
                    <th width="140">{{ __('Priority') }}</th>
                    <th width="120">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>

                @foreach ($tickets as $ticket)
                <tr @if($ticket->closed_at) class="bg-light" @endif>
                    <td>
                        <div class="float-right">
                            @if (! $ticket->last_response && !$ticket->closed_at) <button class="btn btn-sm btn-light text-danger font-weight-bold mr-1 py-0 px-2"><i class="fas fa-exclamation-triangle"></i>
                                {{ __('New') }}</button> @endif
                            @if ($ticket->closed_at) <button class="btn btn-sm btn-dark mr-1 py-0 px-2">{{ __('Closed') }}</button> @endif
                            @if ($ticket->last_response == 'client' && !$ticket->closed_at) <button class="btn btn-sm btn-light text-danger font-weight-bold mr-1 py-0 px-2"><i class="fas fa-exclamation-triangle"></i>
                                {{ __('Waiting your response') }}</button> @endif
                            @if ($ticket->last_response == 'operator' && !$ticket->closed_at) <button class="btn btn-sm btn-light text-success font-weight-bold mr-1 py-0 px-2"><i class="fas fa-reply"></i>
                                {{ __('Waiting client response') }}</button> @endif
                        </div>

                        <h4>
                            <a href="{{ route('admin.tickets.show', ['id'=>$ticket->id]) }}">{{ $ticket->subject }}</a>
                        </h4>

                        <div class="text-muted small">
                            @if($ticket->order_id)<b>{{ __('Ticket automatially created for order ') }} #<a
                                    href="{{ route('admin.cart.orders.show', ['id' => $ticket->order_id]) }}">{{ $ticket->order_code }}</a></b><br>@endif

                            @if ($ticket->file)
                            <div class="mt-2"></div>
                            <a title="{{ $ticket->file }}" target="_blank" href="{{ asset('uploads/'.$ticket->file) }}"><i class="fas fa-file"></i> {{ __('View attachment') }}</a>
                            <div class="mb-2"></div>
                            @endif
                            {{ __('Created at') }}: {{ date_locale($ticket->created_at, 'datetime') }}<br>
                            ID: {{ strtoupper($ticket->code) }}
                            @if($ticket->latest_response_created_at)<br>{{ __('Latest response') }}: {{ date_locale($ticket->latest_response_created_at, 'datetime') }}@endif
                            @if($ticket->closed_at)<br>{{ __('Closed at') }}: {{ date_locale($ticket->closed_at, 'datetime') }}@endif
                            @if($ticket->closed_by_user_name)<br>{{ __('Closed by') }}: {{ $ticket->closed_by_user_name }}@endif
                        </div>
                    </td>

                    <td>
                        @if ($ticket->client_avatar)
                        <span class="float-left mr-2"><img style="max-width:28px; height:auto;" src="{{ image($ticket->client_avatar) }}" /></span>
                        @endif
                        <b>{{ $ticket->client_name}}</b>

                        <div class="clearfix"></div>
                        <div class="mt-2"></div>

                        <div class="text-muted small">
                            {{ $ticket->count_client_open_tickets }} {{ __('open tickets') }}, 
                            {{ $ticket->count_client_tickets }} {{ __('total tickets') }}
                        </div>
                    </td>

                    <td>
                        <b>{{ $ticket->department_title }}</b>
                    </td>

                    <td>
                        @if ($ticket->priority == 0 && ! $ticket->closed_at) <button type="button" class="float-right btn btn-info btn-sm btn-block">{{ __('Normal') }}</button> @endif
                        @if ($ticket->priority == 1 && ! $ticket->closed_at) <button type="button" class="float-right btn btn-warning btn-sm btn-block">{{ __('Important') }}</button> @endif
                        @if ($ticket->priority == 2 && ! $ticket->closed_at) <button type="button" class="float-right btn btn-danger btn-sm btn-block">{{ __('Urgent') }}</button> @endif
                    </td>

                    <td>
                        <a class="btn btn-dark btn-sm btn-block" href="{{ route('admin.tickets.show', ['id'=>$ticket->id]) }}"><i class="fas fa-search" aria-hidden="true"></i> {{ __('View') }}</a>

                        <div class="mt-3"></div>

                        <form method="POST" action="{{ route('admin.tickets.show', ['id' => $ticket->id]) }}">
                            {{ csrf_field() }}
                            {{ method_field('DELETE') }}
                            <button type="submit" class="float-right btn btn-danger btn-sm btn-block delete-item-{{$ticket->id}}"><i class="fas fa-trash-alt"></i> {{ __('Delete') }}</button>
                        </form>

                        <script>
                            $('.delete-item-{{$ticket->id}}').click(function(e){
									e.preventDefault() // Don't post the form, unless confirmed
									if (confirm("{{ __('Are you sure to delete this ticket? All responses will be deleted.') }}")) {
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

    {{ $tickets->appends(['search_terms' => $search_terms, 'search_status' => $search_status, 'search_priority' => $search_priority, 'search_department_id' => $search_department_id])->links() }}

</div>
<!-- end card-body -->