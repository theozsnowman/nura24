<div class="card-header">
    <h3><i class="far fa-user"></i> {{ __('Accounts') }} ({{ $accounts->total() ?? 0 }})</h3>
</div>
<!-- end card-header -->

<div class="card-body">

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if ($message = Session::get('success'))
    <div class="alert alert-success">
        @if ($message=='created') {{ __('Created') }} @endif
        @if ($message=='updated') {{ __('Updated') }} @endif
        @if ($message=='deleted') {{ __('Deleted') }} @endif
    </div>
    @endif

    @if ($message = Session::get('error'))
    <div class="alert alert-danger">
        @if ($message=='duplicate') {{ __('Error. This email exist') }} @endif
    </div>
    @endif
        
    @if(check_access('accounts', 'manager'))
    <div class="pull-right">
        <a class="btn btn-primary" href="#" data-toggle="modal" data-target="#create-account"><i class="fas fa-plus-square" aria-hidden="true"></i> {{ __('Create account') }}</a>
        @include('admin.accounts.modals.create-account')

        
        @if(logged_user()->role == 'admin')
        <div class="dropdown float-right ml-3">
            <button class="btn btn-dark dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-cog"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton" style="min-width: 200px;">
                <a class="dropdown-item" href="{{ route('admin.config.registration') }}">{{ __('Registration') }}</a>
                <a class="dropdown-item" href="{{ route('admin.accounts.permissions') }}">{{ __('Internal permissions') }}</a>
                <a class="dropdown-item" href="{{ route('admin.accounts.tags') }}">{{ __('Users tags') }}</a>
            </div>
        </div>        
        @endif
    </div>
    @endif


    <section>
        <form action="{{ route('admin.accounts') }}" method="get" class="form-inline">

            <input type="text" name="search_terms" placeholder="Search user" class="form-control mr-2 mb-2 @if($search_terms) is-valid @endif" value="{{ $search_terms ?? '' }}" />

            <select name="search_role_id" class="form-control mr-2 mb-2 @if($search_role_id) is-valid @endif">
                <option value="">- {{ __('All roles') }} -</option>
                @foreach ($roles as $role)
                <option @if($search_role_id==$role->id) selected="selected" @endif value="{{ $role->id }}">
                    @switch($role->role)
                    @case('admin')
                    {{ __('Administrator') }}
                    @break                  

                    @case('user')
                    {{ __('Registered user') }}
                    @break

                    @case('internal')
                    {{ __('Internal') }}
                    @break

                    @case('vendor')
                    {{ __('Vendor') }}
                    @break

                    @default
                    {{ $account->role }}
                    @endswitch
                </option>
                @endforeach
            </select>

            <select name="search_tag_id" class="form-control mr-2 mb-2 @if($search_tag_id) is-valid @endif">
                <option selected="selected" value="">- {{ __('Any tag') }} -</option>
                @foreach($tags as $tag)
                <option @if($search_tag_id==$tag->id) selected @endif value="{{ $tag->id }}"> {{ $tag->tag }} 
                    -
                    @switch($tag->role)
                    @case('admin')
                    {{ __('Administrator') }}
                    @break                  

                    @case('user')
                    {{ __('Registered user') }}
                    @break

                    @case('internal')
                    {{ __('Internal') }}
                    @break

                    @case('vendor')
                    {{ __('Vendor') }}
                    @break

                    @default
                    {{ $tag->role }}
                    @endswitch
                </option>
                @endforeach
            </select>

            <select name="search_active" class="form-control mr-2 mb-2 @if($search_active) is-valid @endif">
                <option selected="selected" value="">- {{ __('Any status') }} -</option>
                <option @if($search_active=='1' ) selected @endif value="1"> {{ __('Active accounts') }}</option>
                <option @if($search_active=='0' ) selected @endif value="0"> {{ __('Inactive accounts') }}</option>
            </select>

            <select name="search_email_verified" class="form-control mr-2 mb-2 @if($search_email_verified) is-valid @endif">
                <option selected="selected" value="">- {{ __('Any email status') }} -</option>
                <option @if($search_email_verified=='1' ) selected @endif value="1"> {{ __('Email verified accounts') }}</option>
                <option @if($search_email_verified=='0' ) selected @endif value="0"> {{ __('Email not verified accounts') }}</option>
            </select>

            <button class="btn btn-dark mr-2 mb-2" type="submit" /><i class="fas fa-check"></i> {{ __('Apply') }}</button>
            <a class="btn btn-light mb-2" href="{{ route('admin.accounts') }}"><i class="fas fa-undo"></i></a>
        </form>
    </section>

    <div class="mb-3"></div>

    <div class="table-responsive-md">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>{{ __('Details') }}</th>
                    <th width="260">{{ __('Activity') }}</th>
                    <th width="170">{{ __('Role') }}</th>
                    <th width="100">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>

                @foreach ($accounts as $account)
                <tr>
                    <td>
                        @if ($account->active!=1) <span class="pull-right"><button type="button" class="btn btn-danger btn-sm disabled ml-2">{{ __('Inactive') }}</button></span> @endif
                        @if ($account->email_verified_at==NULL) <span class="pull-right"><button type="button" class="btn btn-warning btn-sm disabled">{{ __('Email not verified') }}</button></span> @endif

                        @if ($account->avatar)
                        <span class="float-left mr-2"><img style="max-width:110px; height:auto;" src="{{ asset('uploads/'.$account->avatar) }}" /></span>
                        @endif

                        @php 
                        if($account->last_activity) $last_activity_minutes = round(abs(strtotime(now()) - strtotime($account->last_activity)) / 60,2);
                        @endphp 

                        <h5><a href="{{ route('admin.accounts.show', ['id' => $account->id]) }}">{{ $account->name }}</a> @if($account->last_activity && $last_activity_minutes < 10)<i title="Online" class="fas fa-circle text-success fa-xs"></i>@endif</h5>
                        {{ $account->email }}<br>
                        {{ __('ID') }}: {{ strtoupper($account->id) }} |
                        {{ __('Code') }}: {{ strtoupper($account->code) ?? null}} |
                        {{ __('Registered') }}: {{ date_locale($account->created_at, 'datetime') }} <br>
                        {{ __('Last activity') }}: @if($account->last_activity){{ date_locale($account->last_activity, 'datetime') }}@else {{ __('never') }}@endif

                        @if($account->user_tags)
                        <div class="mb-2"></div>
                        @foreach ((array)explode(',', $account->user_tags) as $tag)
                        <a href="{{ route('admin.accounts', ['search_tag_id' => explode('@', $tag)[0]]) }}"><span class="mr-2 small"
                                style="background-color: {{ explode('@', $tag)[2] ?? '#b7b7b7' }}; padding: 4px 6px; display: inline; color: #fff; width: 100%;">{{ explode('@', $tag)[1] ?? null }}</span></a>
                        @endforeach
                        @endif
                    </td>

                    <td>
                        @if($account->role == 'user')
                        <div class="small">
                            @if($account->count_paid_orders > 0 || $account->count_unpaid_orders > 0)
                            <h5 class="mb-0">{{ __('Orders') }}</h5>
                            <a @if($account->count_unpaid_orders > 0) class="text-danger font-weight-bold" @endif href="{{ route('admin.cart.orders', ['search_user' => $account->email]) }}">{{ $account->count_unpaid_orders }} {{ __('unpaid orders') }}</a> | 
                            <a href="{{ route('admin.cart.orders', ['search_user' => $account->email]) }}">{{ $account->count_paid_orders }} {{ __('paid orders') }}</a>
                            <div class="mb-3"></div>
                            @endif
                        </div>

                        <div class="small">
                            @if($account->count_open_tickets > 0 || $account->count_closed_tickets > 0)
                            <h5 class="mb-0">{{ __('Support tickets') }}</h5>
                            <a @if($account->count_open_tickets > 0) class="text-danger font-weight-bold" @endif href="{{ route('admin.account.tickets', ['id' => $account->id]) }}">{{ $account->count_open_tickets }} {{ __('open tickets') }}</a> | 
                            <a href="{{ route('admin.account.tickets', ['id' => $account->id]) }}">{{ $account->count_closed_tickets }} {{ __('closed tickets') }}</a>
                            @endif
                        </div>
                        @endif
                    </td>

                    <td>
                        <h5>
                            @switch($account->role)
                            @case('admin')
                            {{ __('Administrator') }}
                            @break                           

                            @case('user')
                            {{ __('Registered user') }}
                            @break

                            @case('internal')
                            {{ __('Internal') }}
                            @break

                            @case('vendor')
                            {{ __('Vendor') }}
                            @break

                            @default
                            {{ $account->role }}
                            @endswitch
                        </h5>
                    </td>

                    <td>
                        <div class="d-flex">
                            <a class="btn btn-primary btn-sm mr-2" href="{{ route('admin.accounts.show', ['id' => $account->id]) }}"><i class="fas fa-edit" aria-hidden="true"></i></a>

                            @if(check_access('accounts', 'manager'))
                            <form method="POST" action="{{ route('admin.accounts.show', ['id'=>$account->id]) }}">
                                {{ csrf_field() }}
                                {{ method_field('DELETE') }}
                                <button type="submit" class="float-right btn btn-danger btn-sm delete-item-{{$account->id}}"><i class="fas fa-trash-alt"></i></button>
                            </form>

                            <script>
                                $('.delete-item-{{$account->id}}').click(function(e){
                                        e.preventDefault() // Don't post the form, unless confirmed
                                        if (confirm("{{ __('Are you sure to delete this account?') }}")) {
                                            $(e.target).closest('form').submit() // Post the surrounding form
                                        }
                                    });
                            </script>
                            @endif
                        </div>
                        
                    </td>
                </tr>
                @endforeach

            </tbody>
        </table>
    </div>

    {{ $accounts->appends(['search_terms' => $search_terms, 'search_active' => $search_active, 'search_email_verified' => $search_email_verified, 'search_role_id' => $search_role_id, 'search_tag_id' => $search_tag_id])->links() }}

</div>
<!-- end card-body -->