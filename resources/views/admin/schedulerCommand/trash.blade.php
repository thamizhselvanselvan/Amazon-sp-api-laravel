@extends('adminlte::page')

@section('title', 'Scheduler')

@section('content_header')
    <h1 class="m-0 text-dark text-center">Command Scheduler Trash</h1>
    <a href="{{ route('command.scheduler.index') }}" class="btn btn-primary btn-sm">
        <i class="fas fa-long-arrow-alt-left"></i> Back
    </a>
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
        </div>
    </div>

    <table class="table table-striped yajra-datatable table-bordered text-center table-sm">

        <thead class="table-info">
            <th>Id</th>
            <th>Command Name</th>
            <th>Execution Time</th>
            <th>Description</th>
            <th>Status</th>
            <th>Action</th>
        </thead>

    </table>
@stop

@section('js')
    <script>
        let yajra_table = $('.yajra-datatable').DataTable({

            processing: true,
            serverSide: true,
            ajax: "{{ route('command.scheduler.form.bin') }}",
            pageLength: 50,
            columns: [{
                    data: 'id',
                    name: 'id',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'command_name',
                    name: 'command_name',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'execution_time',
                    name: 'execution_time',
                },
                {
                    data: 'description',
                    name: 'description',
                },
                {
                    data: 'status',
                    name: 'status',
                },
                {
                    data: 'action',
                    name: 'action',
                },

            ],
        });

        $(document).on('click', '.restore', function() {
            let bool = confirm('Are you sure you want to restore?');
            if (!bool) {
                return false;
            }
        })
    </script>
@stop
