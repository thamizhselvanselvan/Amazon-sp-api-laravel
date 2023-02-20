@extends('adminlte::page')

@section('title', 'Process Management')

@section('css')

@stop
@section('content_header')
    <div class="row">
        <div class="col">

            <h1 class="m-0 text-dark"><b>Process Management Dashboard</b></h1>
        </div>
    </div>
@stop

@section('content')
    <table class="table table-striped yajra-datatable table-bordered text-center table-sm">

        <thead class="table-info">
            <th>Id</th>
            <th>Module</th>
            <th>Description</th>
            <th>Command Name</th>
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
            ajax: "{{ route('process.management.index') }}",
            pageLength: 100,
            bLengthChange: false,
            columns: [{
                    data: 'id',
                    name: 'id',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'module',
                    name: 'module',
                },
                {
                    data: 'description',
                    name: 'description',
                    orderable: false,
                    searchable: false,
                },
                {
                    data: 'command_name',
                    name: 'command_name',
                    // orderable: false,
                    // searchable: false,
                },
                {
                    data: 'command_start_time',
                    name: 'command_start_time',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'command_end_time',
                    name: 'command_end_time',
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
