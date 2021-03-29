<?php
debug_backtrace() || die ("Direct access not permitted"); 
?>
<div class="modal fade custom-modal" tabindex="-1" role="dialog" aria-labelledby="update-variable-{{ $variable->id }}" aria-hidden="true" id="update-variable-{{ $variable->id }}">
    <div class="modal-dialog">
        <div class="modal-content">

            <form action="{{ route('admin.config.variables.show', ['id' => $variable->id]) }}" method="post">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Update') }}</h5>
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">{{ __('Close') }}</span></button>
                </div>

                <div class="modal-body">

                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label>{{ __('Variable name') }}</label>
                                <input class="form-control" name="name" type="text" required value="{{ $variable->name }}" />
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-group">
                                <label>{{ __('Value') }}</label>
                                <textarea class="form-control" name="value" rows="3">{{ $variable->value }}</textarea>
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
