<nav class="nav nav-tabs" id="myTab" role="tablist">
    <a class="nav-item nav-link @if ($menu_section=='config.cart') active @endif" href="{{ route('admin.cart.config.general') }}"><i class="fas fa-shopping-cart" aria-hidden="true"></i> {{ __('Cart config') }}</a>
    <a class="nav-item nav-link @if ($menu_section=='config.currencies') active @endif" href="{{ route('admin.cart.config.currencies') }}"><i class="fas fa-dollar-sign" aria-hidden="true"></i> {{ __('Currency') }}</a>
    <a class="nav-item nav-link @if ($menu_section=='config.gateways') active @endif" href="{{ route('admin.cart.config.gateways') }}"><i class="fas fa-money-check-alt" aria-hidden="true"></i> {{ __('Payment gateways') }}</a>
</nav>