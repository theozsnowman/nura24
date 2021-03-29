<?php
debug_backtrace() || die ("Direct access not permitted"); 
?>
<div class="modal fade custom-modal" tabindex="-1" role="dialog" aria-labelledby="update-list-{{ $list->id }}" aria-hidden="true" id="update-list-{{ $list->id }}">
    <div class="modal-dialog">
        <div class="modal-content">

            <form action="{{ route('admin.email.lists.show', ['id' => $list->id]) }}" method="post">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Update') }}</h5>
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                </div>

                <div class="modal-body">

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>{{ __('List title') }}</label>
                                <input class="form-control" name="title" type="text" required value="{{ $list->title }}" />
                            </div>
                        </div>                       
                      
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>{{ __('Description') }} ({{ __('optional') }})</label>
                                <textarea class="form-control" name="description" rows="4">{{ $list->description }}</textarea>
                            </div>
                        </div>

                    </div>

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-dark"><i class="fas fa-share"></i> {{ __('Update') }}</button>
                </div>

            </form>

        </div>
    </div>
</div>