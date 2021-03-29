<!doctype html>
<html lang="{{ $locale }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ Auth::user()->name }} - {{ __('My account') }}</title>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/all.css') }}">

    <!-- Styles -->
    <link href="{{ asset('templates/user/assets/css/user.css') }}" rel="stylesheet">

    <!-- Favicon -->
    @if($config->favicon)
    <link rel="shortcut icon" href="{{ image($config->favicon) }}">@endif


    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.min.js"></script>

    <style>
        .bd-placeholder-img {
            font-size: 1.125rem;
            text-anchor: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        @media (min-width: 768px) {
            .bd-placeholder-img-lg {
                font-size: 3.5rem;
            }
        }
    </style>

    <!-- Text editor-->
    <script src="{{ asset("assets/plugins/trumbowyg/trumbowyg.min.js") }}"></script>
    <script src="{{ asset("assets/plugins/prism/prism.js") }}"></script>
    <script src="{{ asset("assets/plugins/trumbowyg/plugins/highlight/trumbowyg.highlight.min.js") }}"></script>
    <script src="{{ asset("assets/plugins/trumbowyg/plugins/noembed/trumbowyg.noembed.min.js") }}"></script>
    <link rel="stylesheet" href="{{ asset("assets/plugins/prism/prism.css") }}">
    <link rel="stylesheet" href="{{ asset("assets/plugins/trumbowyg/plugins/highlight/ui/trumbowyg.highlight.min.css") }}">
    <link rel="stylesheet" href="{{ asset("assets/plugins/trumbowyg/ui/trumbowyg.min.css") }}">

    <!-- DateTime picker -->
    <script src="{{ asset("assets/plugins/datepicker/gijgo.min.js") }}" type="text/javascript"></script>
    <link href="{{ asset("assets/plugins/datepicker/gijgo.min.css") }}" rel="stylesheet" type="text/css" />

</head>

<body>


    <nav class="navbar navbar-expand-lg navbar-dark navbar-top bg-navbar rounded">

        <div class="container">

            <a title="{{ $site_short_title ?? null }}" href="{{ route('homepage', ['lang' => $lang]) }}" class="navbar-brand home"><img src="{{ image($config->logo) }}" alt="{{ $site_short_title ?? null }}"><span
                    class="sr-only">{{ __('Home') }}</span></a>

            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExample09" aria-controls="navbarsExample09" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarsExample09">
                <ul class="navbar-nav mr-auto">

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('homepage', ['lang' => $lang]) }}" title="{{ $site_short_title ?? null }}">{{ __('Home') }}</a>
                    </li>

                </ul>

                <ul class="navbar-nav ml-auto">

                    @if(logged_user()->count_basket_items > 0)
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('cart.basket', ['lang' => $lang]) }}" title="{{ __('Shopping cart') }}"><i class="fas fa-shopping-cart"></i> <span
                                class="badge badge-danger">{{ logged_user()->count_basket_items }}</span></a>
                    </li>
                    @endif

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="dropdown09" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            @if(Auth::user()->avatar) <img class="logged_user_avatar rounded-circle" src="{{ thumb(Auth::user()->avatar) }}">@else<i class="fas fa-user"></i>@endif
                            {{ strtok(Auth::user()->name, ' ') }}
                        </a>
                        <div class="dropdown-menu" aria-labelledby="dropdown09">
                            <a class="dropdown-item" href="{{ route('user.profile', ['lang' => $lang]) }}"><i class="fas fa-user"></i> {{ __('My profile') }}</a>
                            <a class="dropdown-item" href="{{ route('logout', ['lang' => $lang]) }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fas fa-power-off"></i>
                                {{ __('Sign out') }}</a>
                            <form id="logout-form" action="{{ route('logout', ['lang' => $lang]) }}" method="post" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </li>

                    @if(count(languages()) > 1)
                    <li class="nav-item dropdown">
                        <a class="nav-link  dropdown-toggle" href="#" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-globe"></i> {{ $locale }}
                        </a>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            @foreach (languages() as $nav_lang)
                            <a class="dropdown-item @if($locale == $nav_lang->code) font-weight-bold @endif" @if($nav_lang->is_default == 1) href="{{ env('APP_URL') }}/login/user" @else href="{{ env('APP_URL') }}/{{ $nav_lang->code }}/login/user" @endif>{{ $nav_lang->name }}</a>                                             
                            @endforeach
                        </div>
                    </li>
                    @endif

                </ul>

            </div>

        </div>

    </nav>

    <nav class="navbar navbar-expand-lg navbar-light navbar-menu bg-dark">

        <div class="container">

            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExample10" aria-controls="navbarsExample10" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse " id="navbarsExample10">
                <ul class="navbar-nav">                                        
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('user.profile', ['lang' => $lang]) }}"><i class="far fa-id-card"></i> {{ __('Profile') }}</a>
                    </li>

                    @if(check_module('cart'))
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('user.orders', ['lang' => $lang]) }}"><i class="fas fa-th"></i> {{ __('Orders') }}</a>
                    </li> 
                    
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('user.downloads', ['lang' => $lang]) }}"><i class="fas fa-download"></i> {{ __('Downloads') }}</a>
                    </li> 
                    @endif

                    @if(check_module('cart') || check_module('tickets'))
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('user.tickets', ['lang' => $lang]) }}"><i class="fas fa-ticket-alt"></i> {{ __('Support Tickets') }}</a>
                    </li>                  
                    @endif                   

                    @if(check_module('forum'))
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="dropdown2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-comments"></i> {{ __('Forum') }}</a>
                        <div class="dropdown-menu" aria-labelledby="dropdown2">
                            <a class="dropdown-item" href="{{ route('user.forum.topics') }}">{{ __('My topics') }}</a>
                            <a class="dropdown-item" href="{{ route('user.forum.posts') }}">{{ __('My posts') }}</a>
                            <a class="dropdown-item" href="{{ route('user.forum.warnings') }}">{{ __('Warnings') }}</a>
                            <a class="dropdown-item" href="{{ route('user.forum.restrictions') }}">{{ __('Restrictions') }}</a>
                            <a class="dropdown-item" href="{{ route('user.forum.config') }}">{{ __('Settings') }}</a>
                        </div>
                    </li>
                    @endif

                    @if(file_exists("templates/user/custom/custom-nav.blade.php"))
                    @include("user.custom.custom-nav")
                    @endif
                </ul>
            </div>

        </div>
    </nav>

    <div class="container">
        <div class="row">
            <div class="col-12">
                @include("user.{$view_file}")
            </div>
        </div>
    </div>


    <script>
        $(document).ready(function() {
            'use strict';
            $('.editor').trumbowyg({
                btns: [
                    ['formatting', 'strong', 'em', 'highlight'],
                    ['link', 'noembed'],
                    ['unorderedList', 'orderedList', 'horizontalRule', 'removeformat'],
                ]
            });	   

            bsCustomFileInput.init();     
           
        });       
    </script>


</body>

</html>