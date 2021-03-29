<nav class="nav nav-tabs" id="myTab" role="tablist">
    <a class="nav-item nav-link @if ($menu_tab=='details') active @endif" href="{{ route('admin.tickets.show', ['id' => $ticket->id ]) }}"><i class="fas fa-comments" aria-hidden="true"></i> {{ __('Ticket details') }}</a>
    <a class="nav-item nav-link @if ($menu_tab=='internal') active @endif" href="{{ route('admin.ticket.internal_info', ['id' => $ticket->id ]) }}"><i class="fas fa-exclamation-circle" aria-hidden="true"></i> {{ __('Internal info') }} ({{ $count_internal_info }})</a>
    <a class="nav-item nav-link" target="_blank" href="{{ route('admin.accounts.show', ['id' => $ticket->user_id ]) }}"><i class="fas fa-user" aria-hidden="true"></i> {{ __('Client details') }}</a>    
</nav>