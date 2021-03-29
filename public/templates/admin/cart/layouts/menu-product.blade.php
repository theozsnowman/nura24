<nav class="nav nav-tabs" id="myTab" role="tablist">
    <a class="nav-item nav-link @if ($menu_tab=='details') active @endif" href="{{ route('admin.cart.products.show', ['id'=>$product->id ]) }}"><i class="fas fa-th" aria-hidden="true"></i>
        {{ __('Product details') }}</a>   

    <a class="nav-item nav-link @if ($menu_tab=='images') active @endif" href="{{ route('admin.cart.product.images', ['id'=>$product->id]) }}"><i class="far fa-file-image" aria-hidden="true"></i>
        {{ __('Images') }}</a>

    @if($product->type == 'download')<a class="nav-item nav-link @if ($menu_tab=='files') active @endif" href="{{ route('admin.cart.product.files', ['id'=>$product->id]) }}"><i class="far fa-file" aria-hidden="true"></i> {{ __('Files') }}</a>@endif

    @if(count($extra_langs)>0) 
    <a class="nav-item nav-link @if ($menu_tab=='translates') active @endif" href="{{ route('admin.cart.product.translate', ['id'=>$product->id]) }}"><i class="far fa-flag" aria-hidden="true"></i> {{ __('Translates') }}</a> @endif

</nav>