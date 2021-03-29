<div class="account-header">
    <a class="btn btn-success btn-sm float-right" href="{{ route('user.tickets.create') }}"><i class="fas fa-ticket-alt"></i> {{ __('Create ticket') }}</a>
    <h3><i class="fas fa-ticket-alt"></i> {{ __('Support tickets') }} ({{ $tickets->total() ?? 0 }})</h3>
</div>
<!-- end card-header -->


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

@if($config->tickets_announcement ?? null)
    <div class="alert alert-warning">
        {!! nl2br($config->tickets_announcement) !!}
    </div>
@endif

<div class="table-responsive-md">
    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>{{ __('Details') }}</th>
                <th width="220">{{ __('Latest response') }}</th>
                <th width="120">{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>

            @foreach ($tickets as $ticket)
            <tr>
                <td>
                    @if ($ticket->priority == 1 && !$ticket->closed_at) <button type="button" class="float-right btn btn-warning btn-sm ml-2 py-0 px-2">{{ __('Important') }}</button> @endif
                    @if ($ticket->priority == 2 && !$ticket->closed_at) <button type="button" class="float-right btn btn-danger btn-sm ml-2 py-0 px-2">{{ __('Urgent') }}</button> @endif

                    <h4>
                        @if (! $ticket->last_response && !$ticket->closed_at) <button class="btn btn-sm btn-light text-success font-weight-bold mr-1 py-0 px-2">{{ __('New') }}</button> @endif
                        @if ($ticket->closed_at) <button class="btn btn-sm btn-dark mr-1 py-0 px-2">{{ __('Closed') }}</button> @endif
                        @if ($ticket->last_response == 'operator' && !$ticket->closed_at) <button class="btn btn-sm btn-light text-danger font-weight-bold mr-1 py-0 px-2"><i class="fas fa-exclamation-triangle"></i> {{ __('Waiting your response') }}</button> @endif
                        @if ($ticket->last_response == 'client' && !$ticket->closed_at) <button class="btn btn-sm btn-light text-success font-weight-bold mr-1 py-0 px-2"><i class="fas fa-reply"></i></button> @endif
                        <a href="{{ route('user.tickets.show', ['lang' => $lang, 'code' => $ticket->code]) }}">{{ $ticket->subject }}</a>
                    </h4>

                    <div class="text-muted small">
                        @if($ticket->order_id)<b>{{ __('Ticket automatially created for order ') }} #<a href="{{ route('user.orders.show', ['lang' => $lang, 'code' => $ticket->order_code]) }}">{{ $ticket->order_code }}</a></b><br>@endif
                        {{ __('Created') }} {{ date_locale($ticket->created_at, 'datetime') }}
                        @if($ticket->closed_at)<br>{{ __('Closed') }} {{ date_locale($ticket->closed_at, 'datetime') }}@endif
                        @if($ticket->closed_by_user_name) {{ __(' by') }} {{ $ticket->closed_by_user_name }}@endif
                    </div>
                </td>

                <td>
                    <div class="text-muted small">
                        @if (! $ticket->last_response && !$ticket->closed_at) {{ __(' No response yet') }}@endif
                        @if ($ticket->last_response == 'client') <b>{{ __('You responded at') }}</b><br>{{ date_locale($ticket->latest_response_created_at, 'datetime') }} @endif
                        @if ($ticket->last_response == 'operator') <b>{{ __('Operator responded at') }}</b><br>{{ date_locale($ticket->latest_response_created_at, 'datetime') }} @endif
                    </div>
                </td>

                <td>
                    <a class="btn btn-dark btn-sm btn-block" href="{{ route('user.tickets.show', ['lang' => $lang, 'code' => $ticket->code]) }}"><i class="fas fa-search" aria-hidden="true"></i> {{ __('View') }}</a>
                </td>
            </tr>
            @endforeach

        </tbody>
    </table>
</div>

{{ $tickets->links() }}