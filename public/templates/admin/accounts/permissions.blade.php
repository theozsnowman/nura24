<div class="card-header">
    <h3><i class="fas fa-user-cog"></i> {{ __('Internal permissions') }}</h3>
</div>
<!-- end card-header -->

<div class="card-body">

    @if ($message = Session::get('success'))
    <div class="alert alert-success">
        @if ($message=='updated') {{ __('Updated') }} @endif
    </div>
    @endif


    <span class="pull-right">
        <a class="btn btn-primary" href="#" data-toggle="modal" data-target="#permissionsHelp"><i class="fas fa-question-circle" aria-hidden="true"></i> {{ __('Help related to permissions') }}</a>
        @include('admin.accounts.modals.permissions-help')
    </span>

    <section>
        <form action="{{ route('admin.accounts.permissions') }}" method="get" class="form-inline">

            <input type="text" name="search_terms" placeholder="Search user" class="form-control mr-2 mb-2 mb-sm-0 @if($search_terms) is-valid @endif" value="{{ $search_terms ?? '' }}" />

            <button class="btn btn-dark mr-2 mb-2 mb-sm-0" type="submit" /><i class="fas fa-check"></i> {{ __('Apply') }}</button>
            <a class="btn btn-light mb-2 mb-sm-0" href="{{ route('admin.accounts.permissions') }}"><i class="fas fa-undo"></i></a>
        </form>
    </section>

    <div class="mb-3"></div>

    <form method="post">
        @csrf

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
              

                <tbody>
                    @foreach($internal_accounts as $user)
                    <tr>
                        <td width="240" style="width:250px !important">
                            @if ($user->avatar)
                            <span class="float-left mr-2"><img style="max-width:25px; height:auto;" src="{{ asset('uploads/'.$user->avatar) }}" /></span>
                            @endif
                            <a target="_blank" href="{{ route('admin.accounts.show', ['id' => $user->id]) }}"><b>{{ $user->name }}</b></a>
                            <div class="clearfix"></div>
                            {{ $user->email }}
                        </td>

                        <td>
                            <div class="row">
                                @foreach($modules_permissions as $module_permissions)

                                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12 mb-3">
                                    <label>{{ $module_permissions->module_label }}</label>
                                    <select class="form-control" name="{{ $module_permissions->module_id }}_{{ $user->id}}">
                                        <option @if(! chekbox_permissions($module_permissions->module_id, $user->id)) selected @endif value="0">- {{ __('No access') }} -</option>
                                        @foreach($module_permissions->permissions as $permission)
                                        <option @if(chekbox_permissions($permission->id, $user->id)) selected @endif value="{{ $permission->id }}">{{ $permission->label }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                @endforeach
                            </div>
                        </td>

                    </tr>
                    @endforeach
                </tbody>

            </table>
        </div>


        <input class="btn btn-danger" type="submit" name="action" value="{{ __('Update permissions') }}">

    </form>

</div>
<!-- end card-body -->