@extends('adminlte::page')

@section('title', 'File Management')

@section('css')

@stop
@section('content_header')
    <div class="row">
        <div class="col">

            <h1 class="m-0 text-dark">File Management</h1>
        </div>
    </div>
@stop

@section('content')
    <table class="table table-striped yajra-datatable table-bordered text-center table-sm">

        <thead class="table-info">
            <th>Id</th>
            <th>User Name</th>
            <th>Type</th>
            <th>Module</th>
            <th>Start Time</th>
            <th>End Time</th>
            <th>Processed Time</th>
            <th>Status</th>
        </thead>

    </table>
@stop

@section('js')
    <script>
        let yajra_table = $('.yajra-datatable').DataTable({

            processing: true,
            serverSide: true,
            ajax: "{{ url('/admin/file-management') }}",
            pageLength: 100,
            bLengthChange: false,
            columns: [{
                    data: 'id',
                    name: 'id',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'user_name',
                    name: 'user_name',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'type',
                    name: 'type',
                },
                {
                    data: 'module',
                    name: 'module',
                },
                {
                    data: 'start_time',
                    name: 'start_time',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'end_time',
                    name: 'end_time',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'processed_time',
                    name: 'processed_time',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'status',
                    name: 'status',
                    orderable: false,
                    searchable: false
                },

            ],
        });
    </script>
@stop
