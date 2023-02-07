@extends('adminlte::page')

@section('title', 'All Users with roles')

@section('content_header')
<h1 class="m-0 text-dark">Trash Admin And User Lists</h1>
<div class="row mt-2">
    <div class="col">
        <a href="{{ route('user.home') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-long-arrow-alt-left"></i> Back
        </a>
    </div>
</div>
@stop

@section('content')
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
@stop

@section('js')
    <script type="text/javascript">
        $(function() {

            let yajra_table = $('.yajra-datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ url('v2/master/users/bin') }}",
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
                        data: 'role',
                        name: 'role'
                    },
                    {
                        data: 'action',
                        // name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

        });
    </script>
@stop