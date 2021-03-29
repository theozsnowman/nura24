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
        @if ($message=='created') {{ __('Created') }} @endif
        @if ($message=='updated') {{ __('Updated') }} @endif
        @if ($message=='deleted') {{ __('Deleted') }} @endif
    </div>
    @endif
       
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
                                    href="{{ route('user.orders.show', ['code' => $ticket->order_code]) }}">{{ $ticket->order_code }}</a></b><br>@endif

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

                    </td>
                </tr>
                @endforeach

            </tbody>
        </table>
    </div>

    {{ $tickets->appends(['id' => $account->id])->links() }}

</div>
<!-- end card-body -->