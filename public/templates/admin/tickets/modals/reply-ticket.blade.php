<?php
debug_backtrace() || die ("Direct access not permitted"); 
?>
<div class="modal fade custom-modal" tabindex="-1" role="dialog" aria-labelledby="reply-ticket-{{ $ticket->id }}" aria-hidden="true" id="reply-ticket-{{ $ticket->id }}">
    <div class="modal-dialog">
        <div class="modal-content">

            <form action="{{ route('admin.tickets.reply', ['id' => $ticket->id]) }}" method="post" enctype="multipart/form-data">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Reply to ticket') }}</h5>
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                </div>

                @if(! $ticket->closed_at)
                <div class="modal-body">

                    <div class="row">

                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>Message</label>
                                <textarea class="form-control" name="message" rows="10" required></textarea>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>Upload file (optional)</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="validatedCustomFile" name="file">
                                    <label class="custom-file-label" for="validatedCustomFile">Choose file...</label>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                @endif

                <div class="modal-footer">
                    @if($ticket->closed_at) {{ __('Ticket is closed. You must reopen the ticket to send new message') }} @else
                    <button type="submit" class="btn btn-primary">Reply to ticket</button>
                    @endif
                </div>

            </form>

        </div>
    </div>
</div>