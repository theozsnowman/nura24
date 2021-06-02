<!-- ======= Top Bar ======= -->
<section id="topbar" class="d-flex align-items-center">
    <div class="container d-flex justify-content-center justify-content-md-between">
        <div class="contact-info d-flex align-items-center">
            <i class="bi bi-envelope d-flex align-items-center"><a href="mailto:contact@example.com">contact@example.com</a></i>
            <i class="bi bi-phone d-flex align-items-center ms-4"><span>+1 5589 55488 55</span></i>
        </div>
        <div class="social-links d-none d-md-flex align-items-center">
            <a href="#" class="twitter"><i class="bi bi-twitter"></i></a>
            <a href="#" class="facebook"><i class="bi bi-facebook"></i></a>
            <a href="#" class="instagram"><i class="bi bi-instagram"></i></a>
            <a href="#" class="linkedin"><i class="bi bi-linkedin"></i></i></a>
        </div>
    </div>
</section>

<!-- ======= Header ======= -->
<header id="header" class="d-flex align-items-center">
    <div class="container d-flex align-items-center justify-content-between">

        <h1 class="logo"><a href="{{ homepage() }}"><img alt="" src="{{ image($config->logo) }}"></a></h1>
        
        <nav id="navbar" class="navbar">
            <ul>
                <li class="d-lg-none d-xl-block"><a class="nav-link" href="{{ homepage() }}">Home</a></li>
                <li><a class="nav-link" href="{{ posts_url() }}">Blog</a></li>
                <li><a class="nav-link" href="{{ cart_url() }}">Shop</a></li>
                <li><a class="nav-link" href="{{ forum_url() }}">Forum</a></li>
                <li><a class="nav-link" href="{{ download_url() }}">Downloads</a></li>
                <li class="dropdown"><a href="#"><span>Support</span> <i class="bi bi-chevron-down"></i></a>
                    <ul>
                        <li><a href="{{ docs_url() }}">Knowledge Base</a></li>
                        <li><a href="{{ faq_url() }}">F.A.Q.</a></li>
                        <li><a href="{{ contact_url() }}">Contact Us</a></li>
                    </ul>
                </li>
                
                @if(logged_user())
                    @if(check_module('cart') && logged_user()->role == 'user' && logged_user()->count_basket > 0)
                    <li>
                        <a class="nav-link" href="{{ route('cart.basket', ['lang' => $lang]) }}" title="{{ __('Shopping cart') }}"><i class="bi bi-cart"></i> <span class="badge badge-danger">{{ logged_user()->count_basket }}</span></a>
                    </li>
                    @endif
            
                    <li class="dropdown">
                        <a href="#">
                            <span>
                            @if(logged_user()->avatar)
                            <img class="logged_user_avatar rounded-circle" alt="{{ logged_user()->name }}" src="{{ thumb(logged_user()->avatar) }}">@else<i class="bi bi-person"></i></a>          
                            @endif
                            {{ strtok(logged_user()->name, ' ') }} 
                            </span> <i class="bi bi-chevron-down"></i>                        
                        </a>

                        <ul class="align-end">
                            <li><a href="{{ account_url() }}"><i class="bi bi-gear"></i> @if(logged_user()->role == 'admin'){{ __('Admin area') }}@else {{ __('My account') }}@endif</a></li>

                            <li><a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="bi bi-box-arrow-right"></i> {{ __('Sign out') }}</a></li>
                            <form id="logout-form" action="{{ route('logout') }}" method="post" style="display: none;">
                                @csrf
                            </form>
                        </ul>
                    </li>

                @else

                    @if ($config->registration_enabled == 1)
                    <li>
                        <a href="{{ route('login') }}" class="nav-link">{{ __('Account') }}</a>
                    </li>
                    @endif

                @endif


                @if(count(languages()) > 1)
                    <li class="dropdown"><a href="#" data-bs-toggle="dropdown"><span><i class="bi bi-globe"></i> {{ $locale }}</span> <i class="bi bi-chevron-down"></i></a>
                        <ul class="align-end">
                            @foreach (languages() as $nav_lang)
                            <li><a @if($nav_lang->is_default == 1) href="{{ env('APP_URL') }}" @else href="{{ env('APP_URL') }}/{{ $nav_lang->code }}" @endif>{{ $nav_lang->name }}</a></li>
                            @endforeach
                        </ul>
                    </li>
                @endif                

            </ul>
            <i class="bi bi-list mobile-nav-toggle"></i>
        </nav><!-- .navbar -->

    </div>
</header><!-- End Header -->




