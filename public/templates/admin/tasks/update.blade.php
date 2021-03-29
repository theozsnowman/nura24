<div class="card-header">
    <span class="pull-right">
        <a class="btn btn-primary btn-dark" href="{{ route('admin.tasks') }}"><i class="fas fa-file-alt"></i> {{ __('All task') }}</a>
    </span>
    <h3><i class="far fa-plus-square"></i> {{ __('Update task') }}</h3>
</div>
<!-- end card-header -->

<div class="card-body">

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if ($message = Session::get('error'))
    <div class="alert alert-danger">
        @if ($message=='error_title') {{ __('Error. Input title') }} @endif
    </div>
    @endif

    <form method="post" enctype="multipart/form-data" action="{{ route('admin.tasks.show', ['id'=>$task->id]) }}">
        @csrf

        <div class="row">

            <div class="col-lg-8 col-12">
                <div class="form-group">
                    <label>{{ __('Title') }}</label>
                    <input class="form-control" name="title" type="text" required value="{{ $task->title }}">
                </div>
            </div>

            <div class="col-lg-4 col-12">
                <div class="form-group">
                    <label>{{ __('Priority') }}</label>
                    <select name="priority" class="form-control" required>
                        <option @if($task->priority==0) selected @endif value="0">{{ __('Normal') }}</option>
                        <option @if($task->priority==1) selected @endif value="1">{{ __('Important') }}</option>
                        <option @if($task->priority==2) selected @endif value="2">{{ __('Urgent') }}</option>
                    </select>
                </div>
            </div>

            <div class="col-lg-4 col-12">
                <div class="form-group">
                    <label>{{ __('Asign this task to internal operator') }}</label>
                    <select name="operator_user_id" class="form-control select2" aria-describedby="employeeHelpBlock">
                        <option value="">- {{ __('no operator') }} -</option>
                        @if($employees)
                        @foreach ($employees as $user)
                        <option @if($task->operator_user_id==$user->id) selected @endif value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                        @endif
                    </select>
                    <small id="employeeHelpBlock" class="form-text text-muted">
                        {{ __('Leave empty for no internal. Note that admins and internals with tasks access can manage this task.') }}
                    </small>
                </div>
            </div>         

            <div class="col-lg-4 col-12">
                <div class="form-group">
                    <label>{{ __('Due date') }}</label>
                    <input class="form-control" name="due_date" type="text" id="datepicker_duedate" aria-describedby="duedateHelpBlock"  autocomplete="off" value="{{ $task->due_date }}" />
                    <small id="duedateHelpBlock" class="form-text text-muted">
                        {{ __('Leave empty if task have not any due date') }}
                    </small>
                </div>

                <script>
                    $('#datepicker_duedate').datepicker({
                                uiLibrary: 'bootstrap4',
                                iconsLibrary: 'fontawesome',
                                format: 'yyyy-mm-dd' 
                            });
                </script>
            </div>

            <div class="col-12">
                <div class="form-group">
                    <label>{{ __('Description') }}</label>
                    <textarea class="form-control" rows="10" name="description">{{ $task->description }}</textarea>
                </div>
            </div>

            <div class="col-md-6 col-12">
                <div class="form-group">
                    <label>{{ __('Change file') }} ({{ __('optional') }})</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="validatedCustomFile" name="file">
                        <label class="custom-file-label" for="validatedCustomFile">{{ __('Choose file') }}...</label>
                    </div>
                </div>

            </div>

            <div class="col-12">
                <div class="form-group">
                    <button type="submit" name="status" value="new" class="btn btn-dark"> {{ __('Update') }}</button>
                </div>
            </div>

        </div><!-- end row -->

    </form>

</div>
<!-- end card-body -->