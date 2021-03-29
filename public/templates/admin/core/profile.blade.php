<div class="card-header">
    <h3><i class="far fa-file-alt"></i> {{ __('Profile') }}</h3>
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
        @if ($message=='updated') {{ __('Updated') }} @endif
        @if ($message=='avatar-deleted') {{ __('Deleted') }} @endif
    </div>
    @endif

    @if ($message = Session::get('error'))
    <div class="alert alert-danger">
        @if ($message=='duplicate') {{ __('Error. This email exist') }} @endif
    </div>
    @endif


    <form method="post" enctype="multipart/form-data">
        @csrf

        <div class="row">

            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">

                @if(Auth::user()->avatar)
                <div id="avatar_image">
                    <img class="img-fluid avatar-rounded mb-3" src="/uploads/{{ Auth::user()->avatar }}" />
                    <br>                    
                    <div class="text-danger"><i class="fas fa-times mb-4"></i> <a class="delete_image text-danger" href="/admin/profile/delete-avatar">{{ __('Delete avatar') }}</a></div>
                    <script>
                        $(function() {
                                            $('.delete_image').click(function() {
                                                var id = $(this).attr('id');
        
                                                $.ajax({
                                                    type: "GET",
                                                    url: "{{ route('admin.profile.delete_avatar') }}",
        
                                                    success: function() {
                                                        $('#avatar_image').hide();
                                                        $("#image_deleted_text").html("Deleted").css('color', 'red');
                                                    }
                                                });
                                                return false;
                                            });
                                        });
                    </script>
                </div>
                <div id="image_deleted_text"></div>
                @else
                <img src="{{ asset('/assets/img/no-avatar-big.png') }}" class="img-fluid mb-3">
                @endif

                <div class="form-group">
                    <label>Change avatar</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="validatedCustomFile" name="avatar" aria-describedby="fileHelp">
                        <label class="custom-file-label" for="validatedCustomFile">{{ __('Choose file') }}...</label>
                    </div>
                </div>
            </div>


            <div class="col-lg-9 col-md-8 col-sm-8 col-xs-12">


                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>{{ __('Full name') }} ({{ __('required') }})</label>
                            <input class="form-control" name="name" type="text" value="{{ Auth::user()->name }}" required />
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>{{ __('Valid Email') }} ({{ __('required') }})</label>
                            <input class="form-control" name="email" type="email" value="{{ Auth::user()->email }}" required />
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>{{ __('Password') }} ({{ __('leave empty not to change') }})</label>
                            <input class="form-control" name="password" type="password" value="" autocomplete="new-password" />
                        </div>
                    </div>
                </div>


            </div>

        </div>


        <hr>

        <button type="submit" class="btn btn-primary">{{ __('Update profile') }}</button>

    </form>


</div>
<!-- end card-body -->