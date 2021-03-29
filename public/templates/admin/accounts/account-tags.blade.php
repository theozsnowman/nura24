<div class="card-header">
    <h3><i class="far fa-user"></i> {{ $account->name}} ({{ $account->email}})</h3>
</div>
<!-- end card-header -->

<div class="card-body">

    @include('admin.accounts.layouts.menu-account')
    <div class="mb-3"></div>

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

    @if(check_access('accounts', 'manager'))
    <div class="float-right">
        <a class="btn btn-primary mb-3" href="#" data-toggle="modal" data-target="#add-account-tag"><i class="fas fa-plus-square" aria-hidden="true"></i> {{ __('Add tag') }}</a>
        @include('admin.accounts.modals.add-account-tag')

        
        <div class="dropdown float-right ml-3">
            <button class="btn btn-dark dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-cog"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton" style="min-width: 200px;">
                <a class="dropdown-item" href="{{ route('admin.accounts.tags') }}">{{ __('Manage tags') }}</a>
            </div>
        </div>
    </div>
    @endif

    <div class="table-responsive-md">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>{{ __('Tag') }}</th>
                    <th width="60">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>

                @foreach ($account_tags as $tag)
                <tr>
                    <td>
                        <div style="background-color: {{ $tag->color ?? '#b7b7b7' }}; padding: 5px 10px; display: inline; color: #fff; width: 100%;">{{ $tag->tag }}</div>
                    </td>

                    <td>

                        @if(check_access('accounts', 'manager'))
                        <form method="POST" action="{{ route('admin.account.tags', ['id' => $account->id, 'tag_id' => $tag->id]) }}">
                            {{ csrf_field() }}
                            {{ method_field('DELETE') }}
                            <button type="submit" class="btn btn-danger btn-sm delete-item-{{$tag->id}}"><i class="fas fa-trash-alt"></i></button>
                        </form>

                        <script>
                            $('.delete-item-{{$tag->id}}').click(function(e){
									e.preventDefault() // Don't post the form, unless confirmed
									if (confirm("{{ __('Are you sure to delete this item?') }}")) {
										$(e.target).closest('form').submit() // Post the surrounding form
									}
								});
                        </script>
                        @endif
                    </td>
                </tr>
                @endforeach

            </tbody>
        </table>
    </div>

    {{ $account_tags->appends(['id' => $account->id])->links() }}

</div>
<!-- end card-body -->