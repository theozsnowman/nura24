<div class="card-header">
    <h3><i class="fas fa-bars"></i> {{ __('Dashboard') }} </h3>
</div>
<!-- end card-header -->

<div class="card-body">

    @if (!empty($config->site_offline))
    @if($config->site_offline=='yes')
    <div class="alert alert-danger">
        {{ __('Site is offline') }}. <a href="{{ route('admin.config.site_offline') }}">{{ __('Change') }}</a>
    </div>
    @endif
    @endif

    @if ($message = Session::get('error'))
    <div class="alert alert-danger">
        @if ($message=='demo') {{ __('Error. This action is disabled in demo mode') }} @endif
    </div>
    @endif    

    

   <h3>{{ __('Hello') }}, {{ Auth::user()->name }}</h3>

   <b>{{ __('Your permission are') }}:</b>
   <div class="mb-3"></div>
   
   @foreach($user_permissions as $user_permission)
    <h4>{{ __('Module') }}: {{ $user_permission->module_label }}</h4>
    {{ __('Permission') }}: <b>{{ $user_permission->permission_label }}</b>
    <div class="small text-muted">{{ $user_permission->permission_description }}</div>
    <div class="mb-3"></div>
   @endforeach


</div>