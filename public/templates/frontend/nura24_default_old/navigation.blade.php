<!-- Navbar Start-->
<header class="nav-holder sticky">

    <div id="navbar" role="navigation" class="navbar navbar-expand-lg">

        <div class="container">
                        
            <a title="{{ lang_meta()->site_short_title }}" href="{{ homepage_url() }}" class="navbar-brand home">@if($config->logo)<img src="{{ image($config->logo ?? null) }}" alt="{{ lang_meta()->site_short_title }}">@endif<span
                    class="sr-only">{{ __('Home') }}</span></a>

            <button type="button" data-toggle="collapse" data-target="#navigation" class="navbar-toggler btn-template-outlined"><span class="sr-only">{{ __('Toggle navigation') }}</span><i class="fas fa-align-justify"></i></button>

            <div id="navigation" class="navbar-collapse collapse">
                <ul class="nav navbar-nav ml-auto">
                    <li class="nav-item active">
                        <a href="{{ homepage_url() }}" title="{{ lang_meta()->homepage_meta_title }}">Home</a>
                    </li>              

                    @if(check_module('posts'))
                    <li class="nav-item">
                        <a href="{{ posts_url() }}" title="{{ __('Blog') }}">{{ __('Blog') }}</a>
                    </li>
                    @endif

                    @if(check_module('cart'))
                    <li class="nav-item">
                        <a href="{{ cart_url() }}" title="{{ __('Shop') }}"> {{ __('Shop') }}</a>
                    </li>
                    @endif 

                    @if(check_module('forum'))
                    <li class="nav-item">
                        <a href="{{ forum_url() }}" title="{{ __('Forum') }}"> {{ __('Forum') }}</a>
                    </li>
                    @endif 

                    @if(check_module('downloads'))
                    <li class="nav-item">
                        <a href="{{ download_url() }}" title="{{ __('Downloads') }}">{{ __('Downloads') }}</a>
                    </li>
                    @endif
                    
                    <li class="nav-item dropdown"><a href="#" data-toggle="dropdown" class="dropdown-toggle">{{ __('Help') }} <i class="fas fa-caret-down"></i></a>
                        <ul class="dropdown-menu">  
                            @if(check_module('faq'))<a href="{{ faq_url() }}" class="nav-link">{{ __('F.A.Q.') }}</a>@endif
                            @if(check_module('docs'))<a href="{{ docs_url() }}" class="nav-link">{{ __('Documentation') }}</a>@endif
                            @if(check_module('inbox'))<a href="{{ contact_url() }}" class="nav-link">{{ __('Contact') }}</a>@endif
                        </ul>
                    </li>                    
            
                    @if(logged_user())
                    @if(check_module('cart') && logged_user()->role == 'user' && logged_user()->count_basket > 0)
                    <li class="nav-item">
                        <a href="{{ route('cart.basket', ['lang' => $lang]) }}" title="{{ __('Shopping cart') }}"><i class="fas fa-shopping-cart"></i> <span class="badge badge-danger">{{ logged_user()->count_basket }}</span></a>
                    </li>
                    @endif  
                    @endif

                    @if (Route::has('login'))
                    @auth

                    <li class="nav-item dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            @if(Auth::user()->avatar) 
                                <img class="logged_user_avatar rounded-circle" alt="{{ Auth::user()->name }}" src="{{ thumb(Auth::user()->avatar) }}">@else<i class="fas fa-user"></i>
                            @endif 
                            {{ strtok(Auth::user()->name, ' ') }} <i class="fas fa-caret-down"></i>
                        </a>

                        <ul class="dropdown-menu">

                            @if (logged_user()->role == 'admin')                        
                                <a href="{{ route('admin') }}" class="nav-link"><i class="fas fa-cog"></i> {{ __('Admin area') }}</a>
                            @endif                          

                            @if (logged_user()->role == 'internal')
                            <a href="{{ route('internal') }}" class="nav-link"><i class="fas fa-cog"></i> {{ __('My account') }}</a>
                            @endif

                            @if (logged_user()->role == 'user')                        
                                <a href="{{ route('user') }}" class="nav-link"><i class="fas fa-cog"></i> {{ __('My account') }}</a>
                            @endif

                            <a href="{{ route('logout') }}" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fas fa-power-off"></i> {{ __('Sign out') }}</a>
                            <form id="logout-form" action="{{ route('logout') }}" method="post" style="display: none;">
                                @csrf
                            </form>                               
                        </ul>
                    </li>
                    
                    @else

                    @if ($config->registration_enabled == 1)
                    <li class="nav-item">
                        <a href="{{ route('login') }}" class="nav-link"><i class="fas fa-user"></i> <span class="d-none d-md-inline-block"> {{ __('My account') }}</span></a>
                    </li>
                    @endif
                                                                       
                    @endauth
                    @endif    
                       
                    @if(count(languages())>1)
                    <li class="nav-item dropdown"><a href="#" data-toggle="dropdown" class="dropdown-toggle"><i class="fas fa-globe"></i> {{ $locale }}</a>
                        <ul class="dropdown-menu dropdown-menu-right">
                            @foreach (languages() as $nav_lang)
                            <a @if($nav_lang->is_default == 1) href="{{ env('APP_URL') }}" @else href="{{ env('APP_URL') }}/{{ $nav_lang->code }}" @endif class="nav-link">{{ $nav_lang->name }}</a>
                            @endforeach
                        </ul>
                    </li>
                    @endif
                   
                </ul>
            </div>

        </div>
    </div>
</header>
<!-- Navbar End-->