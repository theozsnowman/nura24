<!-- top bar navigation -->
<div class="headerbar">

    <!-- LOGO -->
    <div class="headerbar-left">
        @if(! ($config->logo_backend ?? null) || ! ($config->license_key ?? null) || ! $sys_valid_license_key)
        <a href="{{ url('/') }}" target="_blank" class="logo d-none d-sm-block"><img
                src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAYAAABXAvmHAAAABmJLR0QA/wD/AP+gvaeTAAADZklEQVRoge2ZS0gWURiG31HLTKKijNKCiCAXXSCzoBYRJGSthKhFixbRhXIXuIggRKJNFyJbtg4KiiChRS2CCMxWRUZXS4uKLnb3Evi0mPlpPJ755+L5zch3NXPmO+/3PTPzz3xzfmlCE/q/5bkwAYok1UjaKGmlpGpJVZLKJRVJ+ibptaQnku5Iuimp3fO8IRf5MwuoBFqAbtKrBzgGVLku6hbQAczJEzMTOA30ZSjc1CDQCsx2BZDTfRsE0AC8cVC4qQ/ANpcAwyCAYuA4MBRRwFfgPLAbWA1UAJOBcmAxsA7YD1wFfuQBaQVKXAHkIKqAixEJnwdFl6fIUQY0Aq8iPK8CU1wBgP1e7wMOAZMyJfJzlQJNwIDFvy3TlYgAMNUFrMxauCXnGuCFJU9rIQA6gUpXxYfyVgTepra6BrA+nRxBzAdeGvk+ALNcAhQaYjkjfxPJb6WEAAAPgXkFgmgxcg0CC5JO7kkB0VEggFLgiZHrmC22yDK2R9KrhLn6MleZR57nDUg6awzvwG8a/w3h91s/jauwxowbt0Se5/VKajOGN5hx4xYgULuxX2MGjHcA8yFRbQaMd4C7Gv6g+PK3CsksYCfwFngMrE4yoTZ4SX0Edo1BjW6F/0mZUz8p+vy/IdtvYFpou1TSijGqJZNsAJ3G/rKxKCSrbAD3jP260SQAFgLN+J+P34AbwGZL3JYgpgeojxvPl7DGeH1/B6ZmLH63pR3IqdmIDTeR3XHj+ZJ6jPyoOJCh+DqiVzByCp/pYYobj0t+1Jj3DChNCdAWUzzA9dECRL2Jz0jqD+0vktSUBkBScWj7l6T1ktYaMbUpPZMLOGPA9wGrUsyvAR7hL3jtC8aWGp6fQvGZrkC+AmYwcgmxG5ib2GSk5ynDrz10zC1AMHmraYDfZqReVgnO/qDh1RQ67h4gMDhugXhBksbqj8ck4Lbh0UtoRRr/VotVFoBi4IrFawA4ApQl8Dhnmd9oxFwuCEBgPhm4FOHZBUT2S8Bhy5xrgGfELQE+FQQgSFACnMT+cnoaMeegJfYBESttwALgAvDFOUAoSQPwzvDtscTtjTmZXSTobZwDBKbTgRP4fdJnYLsl5n0MgBV8TABC5iVE/D9AshW+JADpmjlXAupjIJ4Dm1L4dCeJn9CE/jf9Bq8Q6eYzLRySAAAAAElFTkSuQmCC"
                alt="phpArena" /> <span>nura24</span></a>        
        @else 
        <a href="{{ url('/') }}" target="_blank" class="logo d-none d-sm-block"><span><img src="{{ image($config->logo_backend) }}"></span></a>
        @endif
    </div>

    <nav class="navbar-custom">

        <ul class="list-inline float-right mb-0">

            <li class="list-inline-item dropdown notif">
                <a class="nav-link arrow-none" target="_blank" href="{{ route('homepage') }}" role="button" aria-haspopup="false" aria-expanded="false">
                    <i class="fas fa-globe"></i> {{ __('View website') }}
                </a>
            </li>           

            <li class="list-inline-item dropdown notif">
                <a class="nav-link dropdown-toggle nav-user" data-toggle="dropdown" href="#" aria-haspopup="false" aria-expanded="false">
                    @if(Auth::user()->avatar)
                    <img src="{{ thumb(Auth::user()->avatar) }}" class="img-fluid avatar-circle" />
                    @else
                    <img src="{{ asset('/assets/img/no-avatar-big.png') }}" class="img-fluid avatar-circle">
                    @endif

                </a>
                <div class="dropdown-menu dropdown-menu-right profile-dropdown ">
                    <!-- item-->
                    <div class="dropdown-item noti-title">
                        <h5 class="text-overflow">
                            <small>{{ Auth::user()->name }}</small>
                        </h5>
                    </div>

                    <!-- item-->
                    <a href="{{ route('admin.profile') }}" class="dropdown-item notify-item">
                        <i class="fas fa-user"></i>
                        <span>{{ __('Profile') }}</span>
                    </a>

                    <!-- item-->
                    <a href="{{ route('logout') }}" class="dropdown-item notify-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-power-off"></i>
                        <span>{{ __('Logout') }}</span>
                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>

                </div>
            </li>            

        </ul>

        <ul class="list-inline menu-left mb-0">

            <li class="float-left">
                <button class="button-menu-mobile open-left d-lg-none">
                    <i class="fa fa-fw fa-bars"></i>
                </button>

            </li>

        </ul>

    </nav>

</div>
<!-- End Navigation -->