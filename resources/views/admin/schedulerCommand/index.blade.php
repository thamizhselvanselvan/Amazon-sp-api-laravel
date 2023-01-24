@extends('adminlte::page')

@section('title', 'Scheduler')

@section('css')
    <link rel="stylesheet" href="/css/styles.css">
    <style>
        .checkBox {
            width: 20px;
            height: 20px;
            margin-top: 9px;
        }
    </style>
@stop

@section('content_header')
    <div class="row">
        <div class="col">
            <h1 class="m-0 text-dark text-center"><strong>Command Scheduler</strong> </h1>
        </div>
        <a href="{{ route('command.scheduler.form.bin') }}">
            <x-adminlte-button label="Bin" theme="danger" icon="fas fa-trash" class="btn btn-sm" />
        </a>
    </div>
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
    <div class="row">
        <div class="col-1"></div>
        <div class="col">

            <div class="card">
                <div class="card-body">
                    <form
                        action="{{ $record ?? '' ? route('command.scheduler.form.update') : route('command.scheduler.form.submit') }}"
                        method="POST">
                        @csrf
                        <div class="row">
                            <input type="hidden" name="update_id" value="{{ $record['id'] ?? '' }}">
                            <div class="col">

                                <x-adminlte-input label='Command Name' type='text' name='commandName'
                                    placeholder='Command Name' value="{{ $record['command_name'] ?? '' }}" />
                            </div>
                            <div class="col">
                                <x-adminlte-input label='Execution Time' type='text' name='executionTime'
                                    placeholder='Execution Time' value="{{ $record['execution_time'] ?? '' }}" />
                            </div>
                            <div class="form-group
                                        mt-0 text-center">
                                <label for="Active">Active</label><br>
                                <input type="checkbox" name="status" class="checkBox"
                                    {{ $record['status'] ?? '' == 1 ? 'checked' : '' }}>
                            </div>

                        </div>
                        <x-adminlte-button class="btn-flat" type="submit" label="{{ $record ?? '' ? 'Update' : 'Submit' }}"
                            theme="{{ $record ?? '' ? 'primary' : 'success' }}"
                            icon="{{ $record ?? '' ? 'fa fa-refresh' : 'fas fa-lg fa-save' }}" />
                    </form>
                </div>
            </div>
        </div>
        <div class="col-1"></div>
    </div>

    <table class="table table-striped yajra-datatable table-bordered text-center table-sm">

        <thead class="table-info">
            <th>Id</th>
            <th>Command Name</th>
            <th>Execution Time</th>
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
            bFilter: false,
            lengthChange: false,
            ajax: "{{ route('command.scheduler.index') }}",
            pageLength: 100,
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
                    data: 'status',
                    name: 'status',
                },
                {
                    data: 'action',
                    name: 'action',
                },

            ],
        });

        $(document).on('click', '.remove', function() {
            let bool = confirm('Are you sure you want to delete?');
            if (!bool) {
                return false;
            }
        })
    </script>
@stop
