<?php
debug_backtrace() || die ("Direct access not permitted"); 
?>

<script>
$("#update-ticket-{{ $ticket->id }}").on('shown.bs.modal', function () {
    $(document).off('focusin.modal');
    });
</script>

<div class="modal fade custom-modal" tabindex="-1" role="dialog" aria-labelledby="update-ticket-{{ $ticket->id }}" aria-hidden="true" id="update-ticket-{{ $ticket->id }}">
    <div class="modal-dialog">
        <div class="modal-content">

            <form action="{{ route('admin.tickets.show', ['id' => $ticket->id]) }}" method="post">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Update ticket') }}</h5>
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">{{ __('Close') }}</span></button>
                </div>

                <div class="modal-body">

                    <div class="row">
                        @if(count($departments)>0)
                        <div class="form-group col-md-6">
                            <label>{{ __('Select department') }}</label>
                            <select name="department_id" class="form-control mr-2">
                                <option selected="selected" value="">- {{ __('No department') }} -</option>
                                @foreach ($departments as $department)
                                <option @if($ticket->department_id==$department->id) selected @endif value="{{ $department->id }}"> {{ $department->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif

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
                                <textarea class="form-control editor" name="message" rows="10" required>{{ $ticket->message }}</textarea>
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