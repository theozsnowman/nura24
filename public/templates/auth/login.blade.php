<!doctype html>
<html lang="{{ $locale }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ __('Login') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('/templates/auth/assets/js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('/templates/auth/assets/css/app.css') }}" rel="stylesheet">

    <!-- Favicon -->
    @if($config->favicon ?? null)<link rel="shortcut icon" href="{{ asset("/uploads/$config->favicon") }}">@endif

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">

    <style>
        body {
            min-height: 100vh;
        }

        .border-md {
            border-width: 2px;
        }

        .form-control:not(select) {
            padding: 1.5rem 0.5rem;
        }

        select.form-control {
            height: 52px;
            padding-left: 0.5rem;
        }

        .form-control::placeholder {
            color: #ccc;
            font-weight: bold;
            font-size: 0.9rem;
        }

        .form-control:focus {
            box-shadow: none;
        }
    </style>

</head>

<body>

    <!-- Navbar-->
    <header class="header">
        <nav class="navbar navbar-expand-lg navbar-light py-3">
            <div class="container">
                <!-- Navbar Brand -->
                <a href="{{ route('homepage', ['lang' => $lang]) }}" class="navbar-brand">
                    <img src="{{ image($config->logo_auth ?? $config->logo ?? null) }}" alt="{{ $site_short_title ?? null }}">
                </a>

                @if(count(languages()) > 1)
                <div class="dropdown">
                    <a class="btn btn-lg" href="#" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-globe"></i> {{ $locale }} <i class="fas fa-caret-down"></i>
                    </a>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        @foreach (languages() as $nav_lang)
                        <li class="dropdown-item">
                            <a href="{{ route('login', ['lang' => $nav_lang->code]) }}" class="nav-link">
                                <span @if($locale==$nav_lang->code) class="font-weight-bold" @endif> {{ $nav_lang->name }}</span>
                            </a>
                        </li>
                        @endforeach
                    </div>
                </div>
                @endif

            </div>
        </nav>
    </header>

    <div class="container">

        @if ($message = Session::get('error'))
        <div class="alert alert-danger">
            @if ($message=='login_required') {{ __('Login is required') }}. {{ __('Please login') }} {{ __('or') }} <a href="{{ route('register') }}"><b>{{ __('register new account') }}</b></a>. @endif
        </div>
        @endif

        <div class="row py-5 mt-4 align-items-center">
            <!-- For Demo Purpose -->
            <div class="col-md-5 pr-lg-5 mb-5 mb-md-0 text-center">
                <img src="{{ asset('/templates/auth/assets/img/login.svg') }}" alt="{{ __('Login into your account') }}" class="img-fluid mb-3 d-none d-md-block">
            </div>

            <!-- Registeration Form -->
            <div class="col-md-7 col-lg-6 ml-auto">

                <h4 class='mb-4'>{{ __('Login into your account') }} {{ __('or') }} <a href="{{ route('register') }}">{{ __('Register new account') }}</a></h4>

                <form method="POST" action="{{ route('login', ['lang' => $lang]) }}">
                    @csrf

                    <div class="row">

                        <!-- Email Address -->
                        <div class="input-group col-12 mb-4">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-white px-4 border-md border-right-0">
                                    <i class="fas fa-envelope text-muted"></i>
                                </span>
                            </div>
                            <input id="email" type="email" class="form-control bg-white border-left-0 border-md @error('email') is-invalid @enderror" name="email" placeholder="{{ __('Email') }}"
                                value="{{ old('email') }}" required autocomplete="email">

                            @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror

                        </div>

                        <!-- Password -->
                        <div class="input-group col-12 mb-4">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-white px-4 border-md border-right-0">
                                    <i class="fas fa-lock text-muted"></i>
                                </span>
                            </div>

                            <input id="password" type="password" class="form-control bg-white border-left-0 border-md @error('password') is-invalid @enderror" name="password" placeholder="{{ __('Password') }}" required
                                autocomplete="new-password">

                            @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="input-group col-12 mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                <label class="form-check-label" for="remember">
                                    {{ __('Remember Me') }}
                                </label>
                            </div>
                        </div>
                        
                        <input type="hidden" name="lang" value="{{ $lang }}">
                        
                        <!-- Submit Button -->
                        <div class="form-group col-lg-12 mx-auto mb-0">
                            <button type="submit" class="btn btn-primary btn-block py-2">
                                <span class="font-weight-bold">{{ __('Login') }}</span>
                            </button>
                        </div>

                        <!-- Divider Text -->
                        <div class="form-group col-lg-12 mx-auto d-flex align-items-center my-4">
                            <div class="border-bottom w-100 ml-5"></div>
                            <span class="px-2 small text-muted font-weight-bold text-muted">{{ __('OR') }}</span>
                            <div class="border-bottom w-100 mr-5"></div>
                        </div>


                        <!-- Already Registered -->
                        <div class="text-center w-100">
                            @if (Route::has('password.request'))
                            <p class="text-muted font-weight-bold"><a href="{{ route('password.request', ['locale' => $locale]) }}" class="text-primary ml-2">{{ __('Forgot password') }}</a></p>
                            @endif

                            @if (Route::has('register'))
                            <p class="text-muted font-weight-bold">{{ __('New on website?') }} <a href="{{ route('register') }}" class="text-primary ml-2">{{ __('Register an Account') }}</a></p>
                            @endif
                        </div>

                    </div>

                </form>
            </div>
        </div>
    </div>

</body>

</html>