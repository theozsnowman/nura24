<div class="card-header">
    <h3><i class="fas fa-check"></i> {{ __('Tasks') }} ({{ $tasks->total() ?? 0 }})</h3>
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

    <span class="pull-right">
        <a class="btn btn-primary" href="{{ route('admin.tasks.create') }}"><i class="fas fa-plus-square" aria-hidden="true"></i> {{ __('Add new task') }}</a>
    </span>   

    <section>
        <form action="{{ route('admin.tasks') }}" method="get" class="form-inline">

            <input type="text" name="search_terms" placeholder="{{ __('Search task') }}" class="form-control mr-2" value="{{ $search_terms ?? '' }}" />           

            <select name="search_status" class="form-control mr-2">
                <option selected="search_product_id" value="">- {{ __('All sources') }} -</option>
                <option @if($search_product_id=='0') selected @endif value="0"> {{ __('Manual created') }}</option>
                @foreach($products as $product)
                <option @if($search_product_id==$product->id) selected @endif value="{{ $product->id }}"> {{ $product->title }}</option>
                @endforeach
            </select>

            <select name="search_status" class="form-control mr-2">
                <option selected="selected" value="">- {{ __('All statuses') }} -</option>
                <option @if($search_status=='new' ) selected @endif value="new"> {{ __('New tasks') }}</option>
                <option @if($search_status=='progress' ) selected @endif value="progress"> {{ __('Tasks in progress') }}</option>
                <option @if($search_status=='closed' ) selected @endif value="closed"> {{ __('Closed tasks') }}</option>
            </select>

            <select name="search_priority" class="form-control mr-2">
                <option selected="selected" value="">- {{ __('Any priority') }} -</option>
                <option @if($search_priority=='0' ) selected @endif value="0"> {{ __('Normal') }}</option>
                <option @if($search_priority=='1' ) selected @endif value="1"> {{ __('Important') }}</option>
                <option @if($search_priority=='2' ) selected @endif value="2"> {{ __('Urgent') }}</option>
            </select>

            <button class="btn btn-dark mr-2" type="submit" /><i class="fas fa-check"></i> {{ __('Apply') }}</button>
            <a class="btn btn-light" href="{{ route('admin.tasks') }}"><i class="fas fa-undo"></i> {{ __('Clear all') }}</a>
        </form>
    </section>
    <div class="mb-3"></div>

    <div class="table-responsive-md">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>{{ __('Details') }}</th>
                    <th width="280">{{ __('Operator') }}</th>
                    <th width="280">{{ __('Client') }}</th>
                    <th width="150">{{ __('Due date') }}</th>
                    <th width="140">{{ __('Priority') }}</th>
                    <th width="140">{{ __('Status') }}</th>
                    <th width="100">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>

                @foreach ($tasks as $task)
                <tr>                    
                    <td>
                        
                        <h4><a href="{{ route('admin.tasks.show', ['id'=>$task->id]) }}">{{ $task->title }}</a></h4>                        
                        
                        <div class="text-muted small">
                        {{ __('Created') }}: {{ date_locale($task->created_at, 'datetime') }} {{ __('by') }} {{ $task->author_name }}
                        </div>                        
                    </td>

                    <td>                       
                        @if ($task->operator_avatar)
                        <span class="float-left mr-2"><img style="max-width:25px; height:auto;" src="{{ image($task->operator_avatar) }}" /></span>
                        @endif
                        <h5>{{ $task->operator_name}}</h5>
                    </td>

                    <td>                       
                        @if ($task->client_avatar)
                        <span class="float-left mr-2"><img style="max-width:25px; height:auto;" src="{{ image($task->client_avatar) }}" /></span>
                        @endif
                        <h5>{{ $task->client_name}}</h5>
                    </td>

                    <td>                       
                        @if ($task->due_date)
                        <b>{{ date_locale($task->due_date) }}</b>
                        @else 
                        {{ __('no due date')}}
                        @endif
                    </td>

                    <td>                       
                        @if ($task->priority==0) <button type="button" class="btn btn-info btn-sm btn-block">{{ __('Normal') }}</button> @endif
                        @if ($task->priority==1) <button type="button" class="btn btn-warning btn-sm btn-block">{{ __('Important') }}</button> @endif
                        @if ($task->priority==2) <button type="button" class="btn btn-danger btn-sm btn-block">{{ __('Urgent') }}</button> @endif
                    </td>

                    <td>                       
                        @if ($task->status=='closed') <button type="button" class="btn btn-dark btn-sm btn-block">{{ __('Closed') }}</button> @endif
                        @if ($task->status=='progress') <button type="button" class="btn btn-info btn-sm btn-block">{{ __('In progress') }}</button> @endif
                        @if ($task->status=='new') <button type="button" class="btn btn-danger btn-sm btn-block">{{ __('New') }}</button> @endif
                    </td>

                    <td>
                        <div class="d-flex">
                            <a class="btn btn-primary btn-sm mr-2" href="{{ route('admin.tasks.show', ['id'=>$task->id]) }}"><i class="fas fa-edit" aria-hidden="true"></i></a>                            

                            <form method="POST" action="{{ route('admin.tasks.show', ['id'=>$task->id]) }}">
                                {{ csrf_field() }}
                                {{ method_field('DELETE') }}
                                <button type="submit" class="float-right btn btn-danger btn-sm delete-item-{{$task->id}}"><i class="fas fa-trash-alt"></i></button>
                            </form>
                        </div>

                        <script>
                            $('.delete-item-{{$task->id}}').click(function(e){
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

    {{ $tasks->appends(['search_terms' => $search_terms, 'search_status' => $search_status, 'search_priority' => $search_priority])->links() }}

</div>
<!-- end card-body -->