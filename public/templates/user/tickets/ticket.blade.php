<div class="account-header">
    <h3><i class="fas fa-ticket-alt"></i> {{ __('Ticket details') }}</h3>
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
    @if ($message=='reply_created') {{ __('Message sent') }} @endif
    @if ($message=='response_deleted') {{ __('Response deleted') }} @endif
    @if ($message=='reopened') {{ __('Ticket open') }} @endif
    @if ($message=='updated') {{ __('Updated') }} @endif
    @if ($message=='deleted') {{ __('Deleted') }} @endif
</div>
@endif


<div class="alert alert-info">

    <div class="float-right ml-3">
        @if (! $ticket->last_response && !$ticket->closed_at) <button class="btn btn-sm btn-light text-success font-weight-bold mr-1 py-0 px-2">{{ __('New ticket') }}</button> @endif
        @if ($ticket->closed_at) <button class="btn btn-sm btn-dark mr-1 py-0 px-2">{{ __('Closed ticket') }}</button> @endif

        @if ($ticket->last_response == 'operator' && !$ticket->closed_at) <button class="btn btn-sm btn-light text-danger font-weight-bold mr-1 py-0 px-2"><i class="fas fa-exclamation-triangle"></i>
            {{ __('Waiting your response') }}</button> @endif
        @if ($ticket->last_response == 'client' && !$ticket->closed_at) <button class="btn btn-sm btn-light text-success font-weight-bold mr-1 py-0 px-2"><i class="fas fa-reply"></i>
            {{ __('Waiting operator response') }}</button> @endif
    </div>

    <div class="text-small text-muted mb-3">
        {{ __('Created') }}: <b>{{ date_locale($ticket->created_at, 'datetime') }}</b><br>
        {{ __('Ticket code') }}: <b>{{ strtoupper($ticket->code) }}</b>
    </div>

    <hr>
    <b>{{ $ticket->subject }}</b><br>
    @if ($ticket->file)
    <div class="mt-2"></div>
    <i class="fas fa-link"></i> <a title="{{ $ticket->file }}" target="_blank" href="{{ asset('uploads/'.$ticket->file) }}"> {{ __('View attachment') }}</a>
    <div class="mb-2"></div>
    @endif
    {!! nl2br($ticket->message) !!}
</div>


<h4 class="mt-3 mb-3">{{ __('Latest responses') }} ({{ $responses->total() ?? 0 }})</h4>

<section>
    <form action="{{ route('user.tickets.show', ['lang' => $lang, 'code' => $ticket->code]) }}" method="get" class="form-inline">

        <input type="text" name="responses_search_terms" placeholder="{{ __('Search in responses') }}" class="form-control mr-2" value="{{ $responses_search_terms ?? '' }}" />

        <select name="responses_search_important" class="form-control mr-2">
            <option value="">- {{ __('All responses') }} -</option>
            <option @if($responses_search_important=='important' ) selected @endif value="important">{{ __('Only important responses') }}</option>
        </select>

        <select name="responses_search_author" class="form-control mr-2">
            <option value="">{{ __('Any author') }}</option>
            <option @if($responses_search_author=='client' ) selected @endif value="client">{{ __('Only my responses') }}</option>
            <option @if($responses_search_author=='operator' ) selected @endif value="operator">{{ __('Only operators responses') }}</option>
        </select>

        <button class="btn btn-dark mr-2" type="submit"><i class="fas fa-check"></i> {{ __('Apply') }}</button>
        <a class="btn btn-light" href="{{ route('user.tickets.show', ['lang' => $lang, 'code' => $ticket->code]) }}"><i class="fas fa-undo"></i></a>
    </form>
</section>
<div class="mb-3"></div>

<div class="table-responsive-md">
    <table class="table table-bordered">
        <tbody>

            @foreach ($responses as $response)
            <tr>
                <td>
                    <div class="float-right ml-3">
                        @if ($response->important_for_client == 1)
                        <a title="{{ __('Unmark as important') }}" href="{{ route('user.tickets.unmark_important_response', ['lang' => $lang, 'code' => $ticket->code, 'response_id' => $response->id, ]) }}"
                            class="float-right text-danger"><i class="fas fa-star"></i>
                            {{ __('Important') }}</a>
                        @else
                        <a title="{{ __('Mark as important') }}" href="{{ route('user.tickets.mark_important_response', ['lang' => $lang, 'code' => $ticket->code, 'response_id' => $response->id, ]) }}" class="float-right"><i
                                class="far fa-star text-warning" aria-hidden="true"></i></a>
                        @endif
                    </div>

                    <div class="small text-muted mb-3">
                        <b>{{ ($response->author_name) }}</b> {{ __('at') }} {{ date_locale($response->created_at, 'datetime') }}
                    </div>

                    {!! nl2br($response->message) !!}

                    @if ($response->file)
                    <div class="mt-2"></div>
                    <i class="fas fa-link"></i> <a title="{{ $response->file }}" target="_blank" href="{{ asset('uploads/'.$response->file) }}"> {{ __('View attachment') }}</a>
                    @endif
                </td>

            </tr>
            @endforeach

        </tbody>
    </table>
</div>

<div class="bg-light p-4 mb-4 mt-3">

    <h5>{{ __('Reply to ticket') }}</h5>

    <form action="{{ route('user.tickets.reply', ['lang' => $lang, 'code' => $ticket->code]) }}" method="post" enctype="multipart/form-data">
        @csrf

        @if(! $ticket->closed_at)


        <div class="form-group">
            <textarea class="form-control" name="message" rows="10" required></textarea>
        </div>
        <div class="form-group">
            <label>{{ __('Upload file') }} ({{ __('optional') }})</label>
            <div class="custom-file">
                <input type="file" class="custom-file-input" id="validatedCustomFile" name="file">
                <label class="custom-file-label" for="validatedCustomFile">{{ __('Choose file') }}...</label>
            </div>
        </div>

        @endif

        @if($ticket->closed_at) {{ __('Ticket is closed. You must reopen the ticket to send new message') }} @else
        <button type="submit" class="btn btn-dark">{{ __('Send reply') }}</button>
        @endif

        <div class="float-right mb-3">
            @if(! $ticket->closed_at)
            <a href="{{ route('user.tickets.close', ['lang' => $lang, 'code' => $ticket->code]) }}" class="btn btn-danger"><i class="fas fa-times" aria-hidden="true"></i> {{ __('Close ticket') }}</a>
            @endif

            @if($ticket->closed_at)
            <a href="{{ route('user.tickets.open', ['lang' => $lang, 'code' => $ticket->code]) }}" class="btn btn-danger"><i class="fas fa-check" aria-hidden="true"></i> {{ __('Reopen ticket') }}</a>
            @endif

        </div>

    </form>

</div>

{{ $responses->appends(['responses_search_terms' => $responses_search_terms, 'responses_search_important' => $responses_search_important, 'responses_search_author' => $responses_search_author])->links() }}