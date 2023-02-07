@extends('adminlte::page')

@section('title', 'All Users with roles')

@section('css')

    <link rel="stylesheet" href="/css/styles.css">

@stop

@section('content_header')
    <h1 class="m-0 text-dark">User Lists</h1>

@stop

@section('content')

    <div class="row">
        <div class="col">

            <div class="alert_display">
                @if ($message = Session::get('success'))
                    <div class="alert alert-success alert-block">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        <strong>{{ $message }}</strong>
                    </div>
                @endif
            </div>

            <div class="alert_display">
                @if ($message = Session::get('error'))
                    <div class="alert alert-danger alert-block">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        <strong>{{ $message }}</strong>
                    </div>
                @endif
            </div>

            <h2 class="mb-2">
                <a href="{{ route('users.create') }}">
                    <x-adminlte-button label="Add User" theme="primary" icon="fas fa-plus" />
                </a>
            </h2>

            <table class="table table-bordered yajra-datatable table-striped text-center" style="line-height:12px">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
@stop


@section('js')
    <script type="text/javascript">
        $(function() {

            let yajra_table = $('.yajra-datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ url('v2/master/users') }}",
                pageLength:50,
                columns: [{
                        data: 'id',
                        name: 'id',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'permission',
                        name: 'permission'
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

        });

        $(document).on('click','#remove', function(e){
            e.preventDefault();
            let bool = confirm('Are you sure you want to delete?');
            if (!bool) {
                return false;
            }
            let id = $(this).attr('remove-btn');
            $.ajax({
                method: 'GET',
                url: "/v2/master/users/"+id+"/delete",
                data: {
                    "_token": "{{ csrf_token() }}",
                },
                success: function(response) {
                    alert('User  has been deleted successfully');
                    if(response.success)
                    {
                        getBack();
                    }
                },
            });
            function getBack()
            {
                window.location.href = '/v2/master/users' ;
            }
        });
    </script>
@stop
