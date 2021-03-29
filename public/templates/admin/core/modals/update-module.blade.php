<?php
debug_backtrace() || die ("Direct access not permitted"); 
?>
<div class="modal fade custom-modal" tabindex="-1" role="dialog" aria-labelledby="update-module-{{ $module->id }}" aria-hidden="true" id="update-module-{{ $module->id }}">
    <div class="modal-dialog">
        <div class="modal-content">

            <form action="{{ route('admin.config.modules', ['id' => $module->id]) }}" method="post">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Module status') }}</h5>
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                </div>

                <div class="modal-body">
                  
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('Module status') }}</label>
                                <select name="active" class="form-control">
                                    <option @if ($module->status=='active') selected @endif value="active">{{ __('Active') }}</option>
                                    <option @if ($module->status=='inactive') selected @endif value="inactive">{{ __('Inactive') }}</option>
                                    <option @if ($module->status=='disabled') selected @endif value="disabled">{{ __('Disabled') }}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
                </div>

            </form>

        </div>
    </div>
</div>