<nav class="nav nav-tabs" id="myTab" role="tablist">
<a class="nav-item nav-link @if ($menu_section=='general') active @endif" href="{{ route('admin.config.general') }}"><i class="fas fa-cog" aria-hidden="true"></i> {{ __('General') }}</a>
<a class="nav-item nav-link @if ($menu_section=='registration') active @endif" href="{{ route('admin.config.registration') }}"><i class="fas fa-user" aria-hidden="true"></i> {{ __('Registration') }}</a>
<a class="nav-item nav-link @if ($menu_section=='antispam') active @endif" href="{{ route('admin.config.antispam') }}"><i class="fas fa-shield-alt" aria-hidden="true"></i> {{ __('Antispam') }}</a>
<a class="nav-item nav-link @if ($menu_section=='email') active @endif" href="{{ route('admin.config.email') }}"><i class="fas fa-envelope" aria-hidden="true"></i> {{ __('Email') }}</a>
<a class="nav-item nav-link @if ($menu_section=='contact-page') active @endif" href="{{ route('admin.config.contact') }}"><i class="fas fa-file-alt" aria-hidden="true"></i> {{ __('Contact page') }}</a>
<a class="nav-item nav-link @if ($menu_section=='site-offline') active @endif" href="{{ route('admin.config.site_offline') }}"><i class="fas fa-times" aria-hidden="true"></i> {{ __('Site offline') }}</a>
<a class="nav-item nav-link @if ($menu_section=='variables') active @endif" href="{{ route('admin.config.variables') }}"><i class="fas fa-code" aria-hidden="true"></i> {{ __('Variables') }}</a>
</nav>