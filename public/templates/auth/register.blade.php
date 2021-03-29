<!doctype html>
<html lang="{{ $locale }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ __('Create an Account') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('/templates/auth/assets/js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('/templates/auth/assets/css/app.css') }}" rel="stylesheet">

    <!-- Favicon -->
    @if($config->favicon)<link rel="shortcut icon" href="{{ asset("/uploads/$config->favicon") }}">@endif

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">

    @if($config->registration_recaptcha_enabled=='yes')
    <script src="https://www.google.com/recaptcha/api.js?render={{ $config->google_recaptcha_site_key }}"></script>
    <script>
        grecaptcha.ready(function () {
                grecaptcha.execute('{{ $config->google_recaptcha_site_key }}', { action: 'contact' }).then(function (token) {
                    var recaptchaResponse = document.getElementById('recaptchaResponse');
                    recaptchaResponse.value = token;
                });
            });
    </script>
    @endif

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
                    <img src="{{ image($config->logo_auth ?? $config->logo) }}" alt="{{ $site_short_title ?? null }}"> 
                </a>

                @if(count(languages())>1)
                <div class="dropdown">
                    <a class="btn btn-lg" href="#" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-globe"></i> {{ $locale }} <i class="fas fa-caret-down"></i>
                    </a>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        @foreach (languages() as $nav_lang)
                        <li class="dropdown-item">
                            <a href="{{ route('register', ['lang' => $nav_lang->code]) }}" class="nav-link">
                                <span @if($locale == $nav_lang->code) class="font-weight-bold" @endif> {{ $nav_lang->name }}</span>
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
            @if ($message=='registration_disabled') {{ __('Registration is disabled') }} @endif
        </div>
        @endif

        @error('recaptcha')
        <div class="text-danger">
            <strong>{{ __('Antispam error') }}</strong>
        </div>
        @enderror

        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="row py-5 mt-4 align-items-center">
            
            <div class="col-md-5 pr-lg-5 mb-5 mb-md-0 text-center">
                <img src="{{ asset('/templates/auth/assets/img/register.png') }}" alt="{{ __('Register account') }}" class="img-fluid mb-3 d-none d-md-block">                
            </div>

            <!-- Registeration Form -->
            <div class="col-md-7 col-lg-6 ml-auto">

                <h3 class='mb-4'>{{ __('Create an Account') }}</h3>

                <form method="POST" action="{{ route('register', ['lang' => $lang]) }}">
                    @csrf

                    <div class="row">

                        <!-- Name -->
                        <div class="input-group col-12 mb-4">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-white px-4 border-md border-right-0">
                                    <i class="fas fa-user text-muted"></i>
                                </span>
                            </div>

                            <input id="name" type="text" class="form-control bg-white border-left-0 border-md @error('name') is-invalid @enderror" name="name" placeholder="{{ __('Name') }}" value="{{ old('name') }}"
                                required autocomplete="name" autofocus>

                            @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror

                        </div>

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
                        <div class="input-group col-lg-6 mb-4">
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

                        <!-- Password Confirmation -->
                        <div class="input-group col-lg-6 mb-4">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-white px-4 border-md border-right-0">
                                    <i class="fa fa-lock text-muted"></i>
                                </span>
                            </div>

                            <input id="password-confirm" type="password" class="form-control bg-white border-left-0 border-md" name="password_confirmation" placeholder="{{ __('Confirm Password') }}" required
                                autocomplete="new-password">

                        </div>

                        <!-- Submit Button -->
                        <div class="form-group col-lg-12 mx-auto mb-0">
                            <button type="submit" class="btn btn-primary btn-block py-2">
                                <span class="font-weight-bold">{{ __('Create your account') }}</span>
                            </button>
                        </div>

                        <div class="text-center w-100">
                        <p class="font-italic text-muted text-center mt-3">{{ __("By clicking 'Create an Account', you're agreeing to our") }} <a href="{{ $config->terms_conditions_page ?? '#' }}" class="text-muted">
                            <u>{{ __('Terms and Conditions') }}</u></a>
                        </p>
                            

                        <!-- Divider Text -->
                        <div class="form-group col-lg-12 mx-auto d-flex align-items-center my-4">
                            <div class="border-bottom w-100 ml-5"></div>
                            <span class="px-2 small text-muted font-weight-bold text-muted">{{ __('OR') }}</span>
                            <div class="border-bottom w-100 mr-5"></div>
                        </div>


                        <!-- Already Registered -->
                        <div class="text-center w-100">
                            <p class="text-muted font-weight-bold">{{ __('Already Registered?') }} <a href="{{ route('login', ['lang' => $lang]) }}" class="text-primary ml-2">{{ __('Login') }}</a></p>
                        </div>
                        
                    </div>

                    <input type="hidden" name="recaptcha_response" id="recaptchaResponse">

                </form>
            </div>
        </div>
    </div>

</body>

</html>