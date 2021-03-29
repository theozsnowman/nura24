<link href="{{ asset('assets/css/colorpicker.css') }}" rel="stylesheet">
<script src="{{ asset('assets/js/colorpicker.js') }}"></script>

<div class="card-header">
    <h3><i class="fas fa-tags"></i> {{ __('Accounts tags') }} ({{ $tags->total() ?? 0 }})</h3>
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
        @if ($message=='duplicate') {{ __('Error. This tag exist') }} @endif
    </div>
    @endif

    <span class="pull-right">
        <a class="btn btn-primary" href="#" data-toggle="modal" data-target="#create-tag"><i class="fas fa-plus-square" aria-hidden="true"></i> {{ __('Create account tag') }}</a>
        @include('admin.accounts.modals.create-tag')
    </span>

    <section>
        <form action="{{ route('admin.accounts.tags') }}" method="get" class="form-inline">

            <input type="text" name="search_terms" placeholder="Search tag" class="form-control mr-2 mb-2 @if($search_terms) is-valid @endif" value="{{ $search_terms ?? '' }}" />            

            <select name="search_role_id" class="form-control mr-2 mb-2 @if($search_role_id) is-valid @endif">
                <option value="">- {{ __('All roles') }} -</option>
                @foreach ($active_roles as $role)
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

            <button class="btn btn-dark mr-2 mb-2" type="submit" /><i class="fas fa-check"></i> {{ __('Apply') }}</button>
            <a class="btn btn-light mb-2" href="{{ route('admin.accounts.tags') }}"><i class="fas fa-undo"></i></a>
        </form>
    </section>
    
    <div class="mb-2"></div>

    <div class="table-responsive-md">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>{{ __('Tag') }}</th>
                    <th width="180">{{ __('Role') }}</th>
                    <th width="200">{{ __('Accounts') }}</th>
                    <th width="120">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>

                @foreach ($tags as $tag)
                <tr>
                    <td>
                        <div style="background-color: {{ $tag->color ?? '#b7b7b7' }}; padding: 5px 10px; display: inline; color: #fff; width: 100%;">{{ $tag->tag }}</div>
                    </td>

                    <td>
                        <h5>
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
                        </h5>
                    </td>

                    <td>
                        <h5><a href="{{ route('admin.accounts', ['search_tag_id' => $tag->id]) }}">{{ $tag->count_accounts }} {{ __('accounts') }}</a></h5>
                    </td>                  

                    <td>
                        <div class="d-flex">
                            <button data-toggle="modal" data-target="#update-tag-{{ $tag->id }}" class="btn btn-primary btn-sm  mr-2"><i class="fas fa-edit" aria-hidden="true"></i></button>
                            @include('admin.accounts.modals.update-tag')

                            <form method="POST" action="{{ route('admin.accounts.tags.show', ['id'=>$tag->id]) }}">
                                {{ csrf_field() }}
                                {{ method_field('DELETE') }}
                                <button type="submit" class="float-right btn btn-danger btn-sm delete-item-{{$tag->id}}"><i class="fas fa-trash-alt"></i></button>
                            </form>
                        </div>

                        <script>
                            $('.delete-item-{{$tag->id}}').click(function(e){
									e.preventDefault() // Don't post the form, unless confirmed
									if (confirm("{{ __('Are you sure to delete this item?') }}")) {
										$(e.target).closest('form').submit() // Post the surrounding form
									}
								});
                        </script>
                    </td>
                </tr>
                @endforeach

            </tbody>
        </table>
    </div>

    {{ $tags->appends(['search_terms' => $search_terms])->links() }}

</div>
<!-- end card-body -->