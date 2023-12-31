@extends('adminlte::page')

@section('title', 'All Users with roles')

@section('content_header')
    <h1 class="m-0 text-dark">Admin Lists</h1>

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
        
        <table class="table table-bordered yajra-datatable table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
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
        $(function () {

            let yajra_table = $('.yajra-datatable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ url('admin/users_lists') }}",
                    columns: [
                        {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                        {data: 'name', name: 'name'},
                        {data: 'email', name: 'email'},
                        {data: 'action', orderable: false, searchable: false},
                    ]
            });
            
        });
    </script>
@stop