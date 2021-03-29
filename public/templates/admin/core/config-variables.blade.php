<div class="card-header">
    @include('admin.core.layouts.menu-config')
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
        @if ($message=='duplicate') {{ __('Error. This variable exists') }} @endif
    </div>
    @endif

    <span class="pull-right mb-3"><button class="btn btn-primary" data-toggle="modal" data-target="#create-variable"><i class="fas fa-plus" aria-hidden="true"></i> {{ __('Create') }}</button></span>
    @include('admin.core.modals.create-variable')

    <div class="mb-3"></div>

    <div class="table-responsive-md">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th width="300">{{ __('Variable name') }}</th>
                    <th>{{ __('Value') }}</th>
                    <th width="320">{{ __('Template code') }}</th>
                    <th width="100">{{ __('Actions') }}</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($variables as $variable)
                <tr>

                    <td>
                        {{ $variable->name }}
                    </td>

                    <td>
                        {!! nl2br($variable->value) !!}
                    </td>

                    <td>
                        <pre>$config->{{ $variable->name }}</pre>
                    </td>

                    <td>
                        <div class="d-flex">
                            <button data-toggle="modal" data-target="#update-variable-{{ $variable->id }}" class="btn btn-primary btn-sm  mr-2"><i class="fas fa-edit" aria-hidden="true"></i></button>
                            @include('admin.core.modals.update-variable')

                            <form method="POST" action="{{ route('admin.config.variables.show', ['id' => $variable->id]) }}">
                                {{ csrf_field() }}
                                {{ method_field('DELETE') }}
                                <button type="submit" class="btn btn-danger btn-sm delete-item-{{$variable->id}}"><i class="fas fa-trash-alt"></i></button>
                            </form>

                            <script>
                                $('.delete-item-{{$variable->id}}').click(function(e) {
                                    e.preventDefault() // Don't post the form, unless confirmed
                                    if (confirm('Delete this variable?')) {
                                        $(e.target).closest('form').submit() // Post the surrounding form
                                    }
                                });

                            </script>
                        </div>
                    </td>
                </tr>
                @endforeach

            </tbody>
        </table>
    </div>

    {{ $variables->links() }}

</div>
<!-- end card-body -->
