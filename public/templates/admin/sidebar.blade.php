<!-- Left Sidebar -->
<div class="left main-sidebar">

    <div class="sidebar-inner leftscroll">

        <div id="sidebar-menu">

            <ul>

                <li class="submenu">
                    <a @if($active_submenu=='dashboard' ) class="active" @endif href="{{ route('admin') }}"><i class="fas fa-bars"></i><span> {{ __('Dashboard') }} </span> </a>
                </li>

                @if(check_access('accounts'))
                <li class="submenu">
                    <a @if($active_submenu=='accounts' ) class="active" @endif href="{{ route('admin.accounts') }}"><i class="far fa-user"></i><span> {{ __('Accounts') }} </span> </a>
                </li>
                @endif

                @if(check_access('translates'))
                <li class="submenu">
                    <a href="#"><i class="fas fa-tools"></i> <span> {{ __('Configuration') }} </span> <span class="menu-arrow"></span></a>
                    <ul class="list-unstyled">

                        @if(logged_user()->role == 'admin')                       
                        <li @if($active_submenu=='config.general' ) class="active" @endif><a @if($active_submenu=='config.general' ) class="active" @endif
                                href="{{ route('admin.config.general') }}">{{ __('General config') }}</a></li>
                        <li @if($active_submenu=='config.modules' ) class="active" @endif><a @if($active_submenu=='config.modules' ) class="active" @endif
                                href="{{ route('admin.config.modules') }}">{{ __('Modules') }}</a></li>
                        <li @if($active_submenu=='config.template' ) class="active" @endif><a @if($active_submenu=='config.template' ) class="active" @endif
                                href="{{ route('admin.config.template') }}">{{ __('Template') }}</a></li>
                        <li @if($active_submenu=='config.langs' ) class="active" @endif><a @if($active_submenu=='config.langs' ) class="active" @endif
                                href="{{ route('admin.config.langs') }}">{{ __('Languages & Locale') }}</a></li>
                        @endif

                        @if(check_access('translates'))
                        <li @if($active_submenu=='translates' ) class="active" @endif><a @if($active_submenu=='translates' ) class="active" @endif href="{{ route('admin.translates') }}">{{ __('Translates') }}</a></li>
                        @endif

                        @if(logged_user()->role == 'admin')
                        <li @if($active_submenu=='permissions' ) class="active" @endif><a @if($active_submenu=='permissions' ) class="active" @endif
                                href="{{ route('admin.accounts.permissions') }}">{{ __('Internal permissions') }}</a></li>
                        <li @if($active_submenu=='config.tools' ) class="active" @endif><a @if($active_submenu=='config.tools' ) class="active" @endif href="{{ route('admin.tools.server') }}">{{ __('Tools') }}</a></li>
                        @endif
                    </ul>
                </li>
                @endif

                @if(check_access('posts') || check_access('pages') || check_access('downloads') || check_access('slider'))
                <li class="submenu">
                    <a href="#"><i class="fas fa-edit"></i> <span> {{ __('CMS') }} </span> <span class="menu-arrow"></span></a>
                    <ul class="list-unstyled">

                        @if(check_access('pages'))
                        <li @if($active_submenu=='pages' ) class="active" @endif><a @if($active_submenu=='pages' ) class="active" @endif href="{{ route('admin.pages') }}">{{ __('Pages') }}</a></li>
                        @endif

                        @if(check_access('posts'))
                        <li @if($active_submenu=='posts' ) class="active" @endif><a @if($active_submenu=='posts' ) class="active" @endif href="{{ route('admin.posts') }}">{{ __('Posts') }}</a></li>
                        @endif

                        @if(logged_user()->role == 'admin')
                        <li @if($active_submenu=='blocks' ) class="active" @endif><a @if($active_submenu=='blocks' ) class="active" @endif href="{{ route('admin.blocks') }}">{{ __('Content blocks') }}</a></li>
                        @endif

                        @if(check_access('blocks_groups'))
                        <li @if($active_submenu=='blocks.groups' ) class="active" @endif><a @if($active_submenu=='blocks.groups' ) class="active" @endif href="{{ route('admin.blocks.groups') }}">{{ __('Blocks groups') }}</a></li>
                        @endif                                        


                        @if(check_access('slider') && check_admin_module('slider'))
                        <li @if($active_submenu=='slider' ) class="active" @endif><a @if($active_submenu=='slider' ) class="active" @endif href="{{ route('admin.slider') }}">{{ __('Homepage slider') }}</a></li>
                        @endif

                        @if(check_access('downloads') && check_admin_module('downloads'))
                        <li @if($active_submenu=='downloads' ) class="active" @endif><a @if($active_submenu=='downloads' ) class="active" @endif href="{{ route('admin.downloads') }}">{{ __('Downloads') }}</a></li>
                        @endif
                    </ul>
                </li>
                @endif

                @if(check_admin_module('faq') || check_admin_module('docs') || check_admin_module('tickets') || check_admin_module('contact'))
                @if(check_access('faq') || check_access('docs') || check_access('tickets') || check_access('contact'))
                <li class="submenu">
                    <a href="#"><i class="fas fa-ticket-alt"></i> <span> {{ __('HelpDesk') }} </span> <span class="menu-arrow"></span></a>
                    <ul class="list-unstyled">

                        @if(check_access('tickets') && check_admin_module('tickets'))
                        <li @if($active_submenu=='tickets' ) class="active" @endif><a @if($active_submenu=='tickets' ) class="active" @endif href="{{ route('admin.tickets') }}">{{ __('Support Tickets') }}</a></li>
                        @endif

                        @if(check_access('contact') && check_admin_module('contact'))
                        <li @if($active_submenu=='inbox' ) class="active" @endif>
                            <a @if($active_submenu=='inbox' ) class="active" @endif href="{{ route('admin.inbox') }}">{{ __('Contact messages') }} </span> </a>
                        </li>
                        @endif

                        @if(check_access('faq') && check_admin_module('faq'))
                        <li @if($active_submenu=='faq' ) class="active" @endif><a @if($active_submenu=='faq' ) class="active" @endif href="{{ route('admin.faq') }}">{{ __('FAQ Manager') }}</a></li>
                        @endif

                        @if(check_access('docs') && check_admin_module('docs'))
                        <li @if($active_submenu=='docs' ) class="active" @endif><a @if($active_submenu=='docs' ) class="active" @endif href="{{ route('admin.docs') }}">{{ __('Knowledge Base') }}</a></li>
                        @endif
                    </ul>
                </li>
                @endif
                @endif

                @if(check_access('cart') && check_admin_module('cart'))
                <li class="submenu">
                    <a href="#"><i class="fas fa-shopping-cart"></i> <span> {{ __('eCommerce') }} </span> <span class="menu-arrow"></span></a>
                    <ul class="list-unstyled">
                        <li @if($active_submenu=='cart.products' ) class="active" @endif><a @if($active_submenu=='cart.products' ) class="active" @endif href="{{ route('admin.cart.products') }}">
                                {{ __('Products catalog') }}</a></li>

                        @if(logged_user()->role == 'admin')
                        <li @if($active_submenu=='orders' ) class="active" @endif><a @if($active_submenu=='orders' ) class="active" @endif href="{{ route('admin.cart.orders') }}">{{ __('Orders') }}</a></li>
                        <li @if($active_submenu=='cart.categ' ) class="active" @endif><a @if($active_submenu=='cart.categ' ) class="active" @endif href="{{ route('admin.cart.categ') }}">
                                {{ __('Products categories') }}</a></li>
                        <li @if($active_submenu=='cart.config' ) class="active" @endif><a @if($active_submenu=='cart.config' ) class="active" @endif href="{{ route('admin.cart.config.general') }}">
                                {{ __('Commerce config') }}</a></li>
                        @endif
                    </ul>
                </li>
                @endif

                @if(check_access('forum') && check_admin_module('forum'))
                <li class="submenu">
                    <a href="#"><i class="fas fa-comments"></i> <span> {{ __('Community Forum') }} </span> <span class="menu-arrow"></span></a>
                    <ul class="list-unstyled">
                        <li @if($active_submenu=='forum.topics' ) class="active" @endif><a @if($active_submenu=='forum.topics' ) class="active" @endif href="{{ route('admin.forum.topics') }}">{{ __('Topics') }}</a></li>
                        <li @if($active_submenu=='forum.posts' ) class="active" @endif><a @if($active_submenu=='forum.posts' ) class="active" @endif href="{{ route('admin.forum.posts') }}">{{ __('Posts') }}</a></li>
                        <li @if($active_submenu=='forum.reports.topics' ) class="active" @endif><a @if($active_submenu=='forum.reports.topics' ) class="active" @endif
                                href="{{ route('admin.forum.reports.topics') }}">{{ __('Topic reports') }}</a></li>
                        <li @if($active_submenu=='forum.reports.posts' ) class="active" @endif><a @if($active_submenu=='forum.reports.posts' ) class="active" @endif
                                href="{{ route('admin.forum.reports.posts') }}">{{ __('Posts  reports') }}</a></li>

                        @if(logged_user()->role == 'admin')
                        <li @if($active_submenu=='forum.categ' ) class="active" @endif><a @if($active_submenu=='forum.categ' ) class="active" @endif href="{{ route('admin.forum.categ') }}">{{ __('Forum structure') }}</a>
                        </li>
                        <li @if($active_submenu=='forum.config' ) class="active" @endif><a @if($active_submenu=='forum.config' ) class="active" @endif href="{{ route('admin.forum.config') }}">{{ __('Forum config') }}</a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif

                @if(check_access('email_marketing') && check_admin_module('email_marketing'))
                <li class="submenu">
                    <a @if($active_submenu=='email.campaigns' ) class="active" @endif href="{{ route('admin.email.campaigns') }}"><i class="fas fa-envelope-open-text"></i><span> {{ __('Email Marketing') }} </span> </a>
                </li>
                @endif
            </ul>

            <div class="clearfix"></div>

        </div>

        <div class="clearfix"></div>

    </div>

</div>
<!-- End Sidebar -->