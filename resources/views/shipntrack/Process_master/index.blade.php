@extends('adminlte::page')

@section('title', 'Process Master')

@section('css')

<link rel="stylesheet" href="/css/styles.css">
<style>
    .table td {
        padding: 0;
        padding-left: 5px;
    }

    .table th {
        padding: 2;
        padding: 6px;
    }

    #source{
        height: 37px;
    }
</style>
@stop
@section('content_header')
<div class="row">
    <div class="col">
        <h1 class="m-0 text-dark text-center"><strong>Process Management</strong> </h1>
    </div>
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
        <div class="alert_display">
            @if ($message = Session::get('warning'))
            <div class="alert alert-warning alert-block">
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
                <form action="{{ $record ?? '' ? route('snt.process.update') : route('snt.process.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <input type="hidden" name="update_id" value="{{ $record['id'] ?? '' }}">
                        <div class="col-3">
                            <x-adminlte-input label='Source' type='text' name='source' placeholder='Source' value="{{ $record['source'] ?? '' }}" required />
                        </div>
                        <div class="col-3">
                            <x-adminlte-input label='Destination' type='text' name='destination' placeholder='Destination' value="{{ $record['destination'] ?? '' }}" required />
                        </div>
                        <div class="col-3">
                            <x-adminlte-input label='Process Id' type='text' name='process_id' placeholder='Process Id' value="{{ $record['process_id'] ?? '' }}" required />
                        </div>
                        <div class="col-2">
                            <div style="margin-top: 2.0rem;">
                                <x-adminlte-button class="btn-flat" type="submit" label="{{ $record ?? '' ? 'Update' : 'Submit' }}" theme="{{ $record ?? '' ? 'primary' : 'success' }}" icon="{{ $record ?? '' ? 'fa fa-refresh' : 'fas fa-lg fa-save' }}" />
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-1"></div>
</div>



<table class="table table-bordered yajra-datatable table-striped" id="report_table">
    <thead>
        <tr class="table-info">
            <th>ID</th>
            <th>Source</th>
            <th>Destination</th>
            <th>Process ID</th>
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
<script>
    let yajra_table = $('.yajra-datatable').DataTable({

        processing: true,
        serverSide: true,
        bFilter: false,
        lengthChange: false,
        ajax: "{{ route('snt.process.home') }}",
        pageLength: 100,
        columns: [{
                data: 'id',
                name: 'id',
                orderable: false,
                searchable: false
            },
            {
                data: 'source',
                name: 'source',
                orderable: false,
                searchable: false
            },
            {
                data: 'destination',
                name: 'destination',
                orderable: false,
                searchable: false
            },
            {
                data: 'process_id',
                name: 'process_id',
                orderable: false,
                searchable: false
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