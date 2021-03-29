<?php
debug_backtrace() || die ("Direct access not permitted"); 
?>
<div class="modal fade custom-modal" tabindex="-1" role="dialog" aria-labelledby="update-{{ $group->id }}" aria-hidden="true" id="update-{{ $group->id }}">
    <div class="modal-dialog">
        <div class="modal-content">

            <form method="POST" action="{{ route('admin.blocks.groups', ['id' => $group->id]) }}">
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
                                <label>{{ __('Label') }}</label>
                                <input class="form-control" name="label" type="text" required value="{{ $group->label }}" />
                            </div>
                        </div>                                          
                      
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>{{ __('Description') }} ({{ __('optional') }})</label>
                                <textarea class="form-control" name="description" rows="3">{{ $group->description }}</textarea>
                            </div>
                        </div>   

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ __('Active') }}</label>
                                <select name="active" class="form-control">
                                    <option @if ($group->active==1) selected @endif value="1">{{ __('Yes') }}</option>
                                    <option @if ($group->active==0) selected @endif value="0">{{ __('No') }}</option>
                                </select>
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