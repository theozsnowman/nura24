<div class="card-header">
    <h3><i class="fas fa-ticket-alt"></i> {{ __('Ticket internal info') }}</h3>
</div>
<!-- end card-header -->

<div class="card-body">
    
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.tickets') }}">{{ __('Support Tickets') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Ticket internal info') }}</li>
        </ol>                                
    </nav>

    @if ($message = Session::get('success'))
    <div class="alert alert-success">
        @if ($message=='created') {{ __('Created') }} @endif
        @if ($message=='updated') {{ __('Updated') }} @endif
        @if ($message=='deleted') {{ __('Deleted') }} @endif
    </div>
    @endif

    <div class="mt-3 mb-4">
        @include('admin.tickets.layouts.menu-ticket')
    </div>

    <h4>{{ __('Internal info') }} ({{ $infos->total() ?? 0 }})</h4>
    
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle"></i> {{ __('Internal informations are visibile for operators and administrators only. This informations are not visibile by client.') }}
    </div>

    <button data-toggle="modal" data-target="#create-internal-info" class="btn btn-dark float-right mb-3"><i class="fas fa-plus-square" aria-hidden="true"></i> {{ __('Add internal info') }}</button>
    @include('admin.tickets.modals.create-internal-info')    

    <div class="table-responsive-md">
        <table class="table table-bordered">            
            <tbody>

                @foreach ($infos as $info)
                <tr>
                    <td>
                        <div class="float-right">                           
                            <form method="POST" action="{{ route('admin.ticket.internal_info.delete', ['id' => $ticket->id, 'info_id'=>$info->id]) }}">
                                {{ csrf_field() }}
                                {{ method_field('DELETE') }} 

                                <button type="submit" class="btn btn-light text-danger btn-sm delete-item-{{$info->id}}"><i class="fas fa-times"></i> {{ __('Delete info') }}</button>
                            </form>
                        </div>
                        <script>
                            $('.delete-item-{{$info->id}}').click(function(e){
                                            e.preventDefault() // Don't post the form, unless confirmed
                                            if (confirm("{{ __('Are you sure to delete this item?') }}")) {						
                                                $(e.target).closest('form').submit() // Post the surrounding form
                                            }
                                        });
                        </script>

                        @if ($info->author_avatar)
                        <span class="float-left mr-2"><img style="max-width:28px; height:auto;" src="{{ image($info->author_avatar) }}" /></span>
                        @endif
                        <b>{{ ($info->author_name) }}</b> {{ __('at') }} {{ date_locale($info->created_at, 'datetime') }}

                        <div class="clearfix"></div>
                        <div class="mt-3"></div>

                        {!! nl2br($info->message) !!}
                        @if ($info->file)
                        <div class="mt-2"></div>
                        <a target="_blank" href="{{ asset('uploads/'.$info->file) }}"><i class="fas fa-file"></i> {{ __('View file') }}</a>
                        @endif 
                     
                    </td>                    
                </tr>
                @endforeach

            </tbody>
        </table>
    </div>

    {{ $infos->links() }}

</div>
<!-- end card-body -->