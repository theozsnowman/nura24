<nav class="nav nav-tabs" id="myTab" role="tablist">
    <a class="nav-item nav-link @if ($menu_section=='template') active @endif" href="{{ route('admin.config.template') }}"><i class="fas fa-laptop" aria-hidden="true"></i> {{ __('Templates') }}</a>
    <a class="nav-item nav-link @if ($menu_section=='logo') active @endif" href="{{ route('admin.config.logo') }}"><i class="fas fa-file-image" aria-hidden="true"></i> {{ __('Logo and icons') }}</a>
    <a class="nav-item nav-link @if ($menu_section=='tools') active @endif" href="{{ route('admin.config.template.tools') }}"><i class="fas fa-code" aria-hidden="true"></i> {{ __('Template tools') }}</a>
</nav>