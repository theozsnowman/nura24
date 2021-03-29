<nav class="nav nav-tabs" id="myTab" role="tablist">
    <a class="nav-item nav-link @if ($menu_tab=='details') active @endif" href="{{ route('admin.downloads.show', ['id' => $download->id ]) }}"><i class="fas fa-th" aria-hidden="true"></i>
        {{ __('Download details') }}</a>   

    <a class="nav-item nav-link @if ($menu_tab=='files') active @endif" href="{{ route('admin.download.files', ['id' => $download->id]) }}"><i class="far fa-file" aria-hidden="true"></i> {{ __('Files') }}</a>

    <a class="nav-item nav-link @if ($menu_tab=='images') active @endif" href="{{ route('admin.download.images', ['id' => $download->id]) }}"><i class="far fa-file-image" aria-hidden="true"></i>
        {{ __('Images') }}</a>
    
    @if(count($extra_langs)>0) 
    <a class="nav-item nav-link @if ($menu_tab=='translates') active @endif" href="{{ route('admin.download.translate', ['id' => $download->id]) }}"><i class="far fa-flag" aria-hidden="true"></i> {{ __('Translates') }}</a> @endif

</nav>