<div class="account-header">
    <h3><i class="fas fa-ticket-alt"></i> {{ __('Create ticket') }}</h3>
</div>
<!-- end card-header -->

@if ($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

@if($config->tickets_announcement ?? null)
<div class="alert alert-warning">
    {!! nl2br($config->tickets_announcement) !!}
</div>
@endif

<form method="post" action="{{ route('user.tickets') }}" enctype="multipart/form-data">
    @csrf

    <div class="row">
        <div class="form-group col-md-3">
            <label>{{ __('Priority') }}</label>
            <select class="form-control" name="priority">
                <option value="0">{{ __('Normal') }}</option>
                <option value="1">{{ __('Important') }}</option>
                <option value="2">{{ __('Urgent') }}</option>
            </select>
        </div>

        @if(count($departments) > 0)
        <div class="form-group col-md-3">
            <label>{{ __('Department') }}</label>
            <select class="form-control" name="department_id" required>
                <option value="">- {{ __('select') }} -</option>
                @foreach($departments as $department)
                <option @if(count($departments)==1) selected @endif value="{{ $department->id }}">{{ $department->title }}</option>
                @endforeach
            </select>
        </div>
        @endif
    </div>

    <div class="form-group">
        <label>{{ __('Subject') }}</label>
        <input class="form-control" type="text" name="subject" required>
    </div>

    <div class="form-group">
        <label>{{ __('Message') }}</label>
        <textarea class="form-control" name="message" rows="10" required></textarea>
    </div>

    @if(($config->tickets_client_can_upload_files ?? null) == 'yes')
    <div class="form-group">
        <label>{{ __('Upload file') }} ({{ __('optional') }})</label>
        <div class="custom-file">
            <input type="file" class="custom-file-input" id="validatedCustomFile" name="file">
            <label class="custom-file-label" for="validatedCustomFile">{{ __('Choose file') }}...</label>
        </div>
    </div>
    @endif

    <button type="submit" class="btn btn-dark">{{ __('Create ticket') }}</button>

</form>