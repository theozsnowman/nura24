<div class="card-header">
    <h3><i class="far fa-plus-square"></i> {{ __('Create article') }}</h3>
</div>
<!-- end card-header -->

<div class="card-body">

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.docs') }}">{{ __('Knowledge Base') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Create article') }}</li>
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
        @if ($message=='duplicate') {{ __('Error. Article exists') }} @endif
    </div>
    @endif

    <form method="post" action="{{ route('admin.docs') }}" enctype="multipart/form-data" id="identifier">
        @csrf

        <div class="row">
            <div class="col-12">
                <div class=" form-group">
                    <label>{{ __('Title') }}</label>
                    <input class="form-control" name="title" required>
                </div>
            </div>         

            <div class="col-md-3 col-12">
                <div class=" form-group">
                    <label>{{ __('Select category') }}</label>
                    <select name="categ_id" class="form-control mr-2" required>
                        <option value="">- {{ __('select') }} -</option>
                        @foreach ($categories as $categ)
                        @include('admin.docs.loops.categories-add-select-loop', $categ)
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-3 col-12">
                <div class="form-group">
                    <label>{{ __('Slug') }}</label>
                    <input class="form-control" name="slug">
                </div>
            </div>

            <div class="col-md-3 col-12">
                <div class="form-group">
                    <label>{{ __('Article position in this category') }}</label>
                    <input class="form-control" name="position">
                </div>
            </div>

            <div class="col-md-3 col-12">
                <div class="form-group">
                    <label>{{ __('Featured article') }}</label>
                    <div class="mb-1"></div>
                    <input type="checkbox" data-toggle="toggle" name="featured" data-on="{{ __('Yes') }}" data-off="{{ __('No') }}">
                </div>
            </div>

            <div class="col-12">
                <div class="form-group">
                    <label>{{ __('Search terms') }} ({{ __('separated by comma') }})</label>
                    <input class="form-control" name="search_terms">
                </div>
            </div>  

        </div>       

        <div class="form-group">
            <label>{{ __('Content') }}</label>
            <textarea class="form-control editor" name="content"></textarea>
        </div>

        <div class="form-group">
            <button type="submit" name="active" value="1" class="btn btn-dark"><i class="fas fa-share"></i> {{ __('Save and publish') }}</button>
            <button type="submit" name="active" value="0" class="btn btn-light">{{ __('Save draft') }}</button>
        </div>

    </form>

</div>
<!-- end card-body -->