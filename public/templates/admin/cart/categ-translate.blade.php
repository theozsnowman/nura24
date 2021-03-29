<script>
    function showDiv(divId, element)
{
    document.getElementById(divId).style.display = element.value == '' ? 'block' : 'none';
}
</script>

<div class="card-header">
    <h3><i class="far fa-plus-square"></i> {{ __('Translate category') }}</h3>
</div>
<!-- end card-header -->

<div class="card-body">

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.cart.products') }}">{{ __('eCommerce') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.cart.categ') }}">{{ __('Categories') }}</a></li>
            <li class="breadcrumb-item active">{{ $categ->title }}</li>

        </ol>
    </nav>

   
    <form method="post" action="{{ route('admin.cart.categ.translate', ['id' => $categ->id]) }}">
        @csrf

        <div class="row">
            <div class="col-12">               

                @foreach($translate_langs as $lang)               
                <div class="form-group">
                    <label>{{ __('Category title') }} - {{ $lang->name }}</label>
                    <input class="form-control" type="text" name="title_{{ $lang->id }}" @if($lang->is_default) required @endif value="{{ $lang->translated_title }}">
                </div>

                <div class="form-group">
                    <label>{{ __('Description') }} ({{ __('optional') }}) - {{ $lang->name }}</label>
                    <textarea class="form-control" rows="2" name="description_{{ $lang->id }}">{{ $lang->translated_description }}</textarea>
                </div>

                <div class="form-group">
                    <label>{{ __('Category meta title') }} - {{ $lang->name }}</label>
                    <input class="form-control" type="text" name="meta_title_{{ $lang->id }}" value="{{ $lang->translated_meta_title }}">
                </div>

                <div class="form-group">
                    <label>{{ __('Category meta description') }} - {{ $lang->name }}</label>
                    <textarea class="form-control" rows="2" name="meta_description_{{ $lang->id }}">{{ $lang->translated_meta_description }}</textarea>
                </div>

                <hr>
                <div class="mb-4"></div>
                @endforeach
            </div>            

        </div>


        <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>


    </form>

</div>
<!-- end card-body -->