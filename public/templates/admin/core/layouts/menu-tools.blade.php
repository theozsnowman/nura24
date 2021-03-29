<nav class="nav nav-tabs" id="myTab" role="tablist">
    <a class="nav-item nav-link @if ($menu_section=='tools.server') active @endif" href="{{ route('admin.tools.server') }}"><i class="fab fa-php" aria-hidden="true"></i> {{ __('Server Info') }}</a>
    <a class="nav-item nav-link @if ($menu_section=='tools.update') active @endif" href="{{ route('admin.tools.update') }}"><i class="fas fa-download" aria-hidden="true"></i> {{ __('Update') }}</a>
    <a class="nav-item nav-link @if ($menu_section=='tools.backup') active @endif" href="{{ route('admin.tools.backup') }}"><i class="fas fa-database" aria-hidden="true"></i> {{ __('Backup') }}</a>
    <a class="nav-item nav-link @if ($menu_section=='tools.system') active @endif" href="{{ route('admin.tools.system') }}"><i class="fas fa-tools" aria-hidden="true"></i> {{ __('System') }}</a>
    <a class="nav-item nav-link @if ($menu_section=='tools.sitemap') active @endif" href="{{ route('admin.tools.sitemap') }}"><i class="fas fa-sitemap" aria-hidden="true"></i> {{ __('Sitemap') }}</a>
</nav>