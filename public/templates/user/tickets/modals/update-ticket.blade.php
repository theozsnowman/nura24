<?php
debug_backtrace() || die ("Direct access not permitted"); 
?>
<div class="modal fade custom-modal" tabindex="-1" role="dialog" aria-labelledby="update-ticket-{{ $ticket->code }}" aria-hidden="true" id="update-ticket-{{ $ticket->code }}">
    <div class="modal-dialog">
        <div class="modal-content">

            <form action="{{ route('user.tickets.show', ['code' => $ticket->code]) }}" method="post">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Update ticket') }}</h5>
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">{{ __('Close') }}</span></button>
                </div>

                <div class="modal-body">

                    <div class="row">                       

                        <div class="form-group col-md-6">
                            <label>{{ __('Priority') }}</label>
                            <select name="priority" class="form-control" required>
                                <option @if($ticket->priority==0) selected @endif value="0">{{ __('Normal') }}</option>
                                <option @if($ticket->priority==1) selected @endif value="1">{{ __('Important') }}</option>
                                <option @if($ticket->priority==2) selected @endif value="2">{{ __('Urgent') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>{{ __('Subject') }}</label>
                                <input class="form-control" name="subject" type="text" required value="{{ $ticket->subject }}" />
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>{{ __('Message') }}</label>
                                <textarea class="form-control" name="message" rows="10" required>{{ $ticket->message }}</textarea>
                            </div>
                        </div>


                    </div>

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">{{ __('Update ticket') }}</button>
                </div>

            </form>

        </div>
    </div>
</div>