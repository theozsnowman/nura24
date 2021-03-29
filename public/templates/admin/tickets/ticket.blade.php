<div class="card-header">
    <h3><i class="fas fa-ticket-alt"></i> {{ __('Ticket details') }}</h3>
</div>
<!-- end card-header -->

<div class="card-body">

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.tickets') }}">{{ __('Support Tickets') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Ticket details') }}</li>
        </ol>                                
    </nav>

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

    <div class="mt-3 mb-4">
        @include('admin.tickets.layouts.menu-ticket')
    </div>

    <div class="alert alert-info">

        <div class="float-right">
            @if (! $ticket->last_response && !$ticket->closed_at) <button class="btn btn-sm btn-light text-danger font-weight-bold mr-1 py-0 px-2"><i class="fas fa-exclamation-triangle"></i>
                {{ __('New') }}</button> @endif
            @if ($ticket->closed_at) <button class="btn btn-sm btn-dark mr-1 py-0 px-2">{{ __('Closed') }}</button> @endif
            @if ($ticket->last_response == 'client' && !$ticket->closed_at) <button class="btn btn-sm btn-light text-danger font-weight-bold mr-1 py-0 px-2"><i class="fas fa-exclamation-triangle"></i>
                {{ __('Waiting your response') }}</button> @endif
            @if ($ticket->last_response == 'operator' && !$ticket->closed_at) <button class="btn btn-sm btn-light text-success font-weight-bold mr-1 py-0 px-2"><i class="fas fa-reply"></i>
                {{ __('Waiting client response') }}</button> @endif
        </div>

        @if ($ticket->client_avatar)
        <span class="float-left mr-2"><img style="max-width:28px; height:auto;" src="{{ asset('uploads/'.$ticket->client_avatar) }}" /></span>
        @endif
        <h4>{{ $ticket->client_name}}</h4>

        {{ __('Created') }}<b>: {{ date_locale($ticket->created_at) }}</b><br>
        {{ __('Ticket ID') }}<b>: {{ strtoupper($ticket->code) }}</b><br>
        {{ __('Department') }}: @if($ticket->department_id)<b>{{ $ticket->department_title }}</b> @else {{ __('No department') }}@endif

        <hr>
        <b>{{ $ticket->subject }}</b><br>
        @if ($ticket->file)
        <div class="mt-2"></div>
        <a title="{{ $ticket->file }}" target="_blank" href="{{ asset('uploads/'.$ticket->file) }}"><i class="fas fa-file"></i> View attachment</a>
        <div class="mb-2"></div>
        @endif
        {!! nl2br($ticket->message) !!}
    </div>


    <h4 class="mt-3 mb-3">{{ __('Responses') }} ({{ $responses->total() ?? 0 }})</h4>

    <a href="#" data-toggle="modal" data-target="#update-ticket-{{ $ticket->id }}" class="btn btn-primary float-right ml-2"><i class="fas fa-edit" aria-hidden="true"></i></a>
    @include('admin.tickets.modals.update-ticket')

    @if(! $ticket->closed_at)
    <a href="{{ route('admin.tickets.close', ['id' => $ticket->id]) }}" class="btn btn-danger float-right ml-2"><i class="fas fa-times" aria-hidden="true"></i> {{ __('Close ticket') }}</a>
    @endif

    @if($ticket->closed_at)
    <a href="{{ route('admin.tickets.open', ['id' => $ticket->id]) }}" class="btn btn-danger float-right ml-2"><i class="fas fa-check" aria-hidden="true"></i> {{ __('Reopen ticket') }}</a>
    @endif

    <a href="#" data-toggle="modal" data-target="#reply-ticket-{{ $ticket->id }}" class="btn btn-success float-right"><i class="fas fa-reply" aria-hidden="true"></i> {{ __('Reply to ticket') }}</a>
    @include('admin.tickets.modals.reply-ticket')

    <section>
        <form action="{{ route('admin.tickets.show', ['id' => $ticket->id]) }}" method="get" class="form-inline">

            <input type="text" name="responses_search_terms" placeholder="{{ __('Search responses') }}" class="form-control mr-2 @if($responses_search_terms) is-valid @endif" value="{{ $responses_search_terms ?? '' }}" />

            <select name="responses_search_important" class="form-control mr-2 @if($responses_search_important) is-valid @endif">
                <option value="">- {{ __('All responses') }} -</option>
                <option @if($responses_search_important == 'important' ) selected @endif value="important">{{ __('Only important responses') }}</option>
            </select>

            <select name="responses_search_author" class="form-control mr-2 @if($responses_search_author) is-valid @endif ">
                <option value="">- {{ __('Any author') }} -</option>
                <option @if($responses_search_author == 'client' ) selected @endif value="client">{{ __('Only client responses') }}</option>
                <option @if($responses_search_author == 'operator' ) selected @endif value="operator">{{ __('Only operators responses') }}</option>
            </select>

            <button class="btn btn-dark mr-2" type="submit" /><i class="fas fa-check"></i> {{ __('Apply') }}</button>
            <a class="btn btn-light" href="{{ route('admin.tickets.show', ['id'=>$ticket->id]) }}"><i class="fas fa-undo"></i></a>
        </form>
    </section>
    <div class="mb-3"></div>

    <div class="table-responsive-md">
        <table class="table table-bordered">            
            <tbody>

                @foreach ($responses as $response)
                <tr>
                    <td>
                        <form class="form-inline float-right" method="POST" action="{{ route('admin.tickets.responses.delete', ['id'=>$ticket->id, 'response_id'=>$response->id, ]) }}">
                            {{ csrf_field() }}
                            <button type="submit" class="btn btn-light btn-sm text-danger px-2 py-0 ml-2 delete-item-{{$response->id}}"><i class="fas fa-times"></i> {{ __('Delete') }}</button>
                        </form>

                        <script>
                            $('.delete-item-{{$response->id}}').click(function(e){
                                            e.preventDefault() // Don't post the form, unless confirmed
                                            if (confirm("{{ __('Are you sure to delete this item?') }}")) {						
                                                $(e.target).closest('form').submit() // Post the surrounding form
                                            }
                                        });
                        </script>

                        @if ($response->important_for_operator==1)
                        <a href="{{ route('admin.tickets.unmark_important_response', ['id'=>$ticket->id, 'response_id'=>$response->id, ]) }}" class="pull-right text-danger"><i class="fas fa-star"></i>
                            {{ __('Important') }}</a>
                        @else
                        <a href="{{ route('admin.tickets.mark_important_response', ['id'=>$ticket->id, 'response_id'=>$response->id, ]) }}" class="pull-right text-warning"><i class="far fa-star"
                                aria-hidden="true"></i> {{ __('Mark important') }}</a>
                        @endif

                        <div class="text-small mb-3">
                            @if ($response->author_avatar)
                            <span class="float-left mr-2"><img style="max-width:28px; height:auto;" src="{{ image($response->author_avatar) }}" /></span>
                            @endif

                            @if($response->user_id == $ticket->user_id) <button class="btn btn-sm btn-dark px-2 py-0 mr-2">{{ __('client') }}</button> @else <button class="btn btn-sm btn-success px-2 py-0 mr-2">{{ __('operator') }}</button @endif 
                            <b>{{ ($response->author_name) }}</b> 
                            {{ __('at') }} {{ date_locale($response->created_at, 'datetime') }}                            
                        </div>                                                

                        <div class="clearfix"></div>

                        {!! nl2br($response->message) !!}

                        @if ($response->file)
                        <div class="mt-2"></div>
                        <a title="{{ $response->file }}" target="_blank" href="{{ asset('uploads/'.$response->file) }}"><i class="fas fa-file"></i> {{ __('View attachment') }}</a>
                        @endif
                    </td>                                   
                </tr>
                @endforeach

            </tbody>
        </table>
    </div>

    {{ $responses->appends(['responses_search_terms' => $responses_search_terms, 'responses_search_important' => $responses_search_important, 'responses_search_author' => $responses_search_author])->links() }}

</div>
<!-- end card-body -->