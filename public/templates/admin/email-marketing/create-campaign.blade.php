<div class="card-header">
    <h3><i class="far fa-plus-square"></i> {{ __('New campaign') }}</h3>
</div>
<!-- end card-header -->

<div class="card-body">

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.email.campaigns') }}">{{ __('Email campaigns') }}</a></li>
            <li class="breadcrumb-item active">{{ __('New campaign') }}</li>
        </ol>
    </nav>

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
        @if ($message=='duplicate') {{ __('Error. This campaign exists') }} @endif
    </div>
    @endif

    <form method="post" action="{{ route('admin.email.campaigns') }}">
        @csrf

        <div class="row">
            <div class="col-lg-12">
                <div class="form-group">
                    <label>{{ __('Campaign title') }}</label>
                    <input type="text" class="form-control" name="title">
                </div>
            </div>

            <div class="col-lg-12">
                <div class="form-group">
                    <label>{{ __('Campaign description') }} ({{ __('optional') }})</label>
                    <textarea class="form-control" name="description" rows="2"></textarea>
                </div>
            </div>

            <hr>            

            <div class="col-lg-12">
                <div class="form-group">
                    <label>{{ __('Email subject') }}</label>
                    <input type="text" class="form-control" name="subject" required>
                </div>
            </div>

            <div class="col-lg-12">
                <div class="form-group">
                    <label>{{ __('Email content') }}</label>
                    <textarea class="form-control editor" name="content" required></textarea>
                </div>
            </div>

        </div>

        <button type="submit" class="btn btn-dark">{{ __('Create campaign') }}</button>

    </form>

</div>
<!-- end card-body -->