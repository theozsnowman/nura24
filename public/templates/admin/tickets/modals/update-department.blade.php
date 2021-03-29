<?php
debug_backtrace() || die ("Direct access not permitted"); 
?>
<div class="modal fade custom-modal" tabindex="-1" role="dialog" aria-labelledby="update-department-{{ $department->id }}" aria-hidden="true" id="update-department-{{ $department->id }}">
    <div class="modal-dialog">
        <div class="modal-content">

            <form action="{{ route('admin.tickets.departments.show', ['id' => $department->id]) }}" method="post">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Update department') }}</h5>
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">{{ __('Close') }}</span></button>
                </div>

                <div class="modal-body">

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>{{ __('Department title') }}</label>
                                <input class="form-control" name="title" type="text" required value="{{ $department->title }}" />
                            </div>
                        </div>                   

                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>{{ __('Description') }} ({{ __('optional') }})</label>
                                <textarea class="form-control" name="description" rows="2">{{ $department->description }}</textarea>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ __('Active') }}</label>
                                <select name="active" class="form-control">
                                    <option @if ($department->active==1) selected @endif value="1">{{ __('Yes') }}</option>
                                    <option @if ($department->active==0) selected @endif value="0">{{ __('No') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ __('Hidden') }}</label>
                                <select name="hidden" class="form-control" aria-describedby="hiddenHelp">
                                    <option @if ($department->hidden==1) selected @endif value="1">{{ __('Yes') }}</option>
                                    <option @if ($department->hidden==0) selected @endif value="0">{{ __('No') }}</option>
                                </select>
                                <small id="hiddenHelp" class="form-text text-muted">{{ __('Hidden departments can not be selected by clients') }}</small>
                            </div>
                        </div>

                    </div>

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">{{ __('Update department') }}</button>
                </div>

            </form>

        </div>
    </div>
</div>