@extends('adminlte::page')

@section('title', 'Ship $ Track')

@section('content_header')
<div class="row">
    <h1 class="m-0 text-dark col">ShipNTrack Management</h1>
    <h2 class="mb-4 text-right col">
        <!-- <a href="search-invoice">
            <x-adminlte-button label="Search Invoice" theme="primary" icon="fas fa-search" class="btn-sm" />
        </a> -->

        <a href="upload">
            <x-adminlte-button label="Add New Records" theme="primary" icon="fas fa-file-upload" class="btn-sm" />
        </a>
        <a href="template/download">
            <x-adminlte-button label="Download Template" theme="primary" icon="fas fa-download" class="btn-sm" />
        </a>
    </h2>
</div>
@stop

@section('content')
<div class="pl-2">
    <table class="table table-bordered yajra-datatable table-striped text-center table-sm">
        <thead>
            <tr class="text-bold bg-info">
                <th>Sr</th>
                <th>Weight</th>
                <th>Base Rate</th>
                <th>Commission</th>
                <th>source-Destination</th>
            </tr>
        </thead>
        <tbody>
        </tbody>

    </table>
</div>
@stop

@section('js')
<script>
let yajra_table = $('.yajra-datatable').DataTable({

        processing: true,
        serverSide: true,
        ajax: "{{ url('/shipntrack/index') }}",
        pageLength: 50,
        // searching: false,
        columns: [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false
            },
            {
                data: 'weight',
                name: 'weight',
            },
            {
                data: 'base_rate',
                name: 'base_rate',
            },
            {
                data: 'commission',
                name: 'commission',
            },
            {
                data: 'source_destination',
                name: 'source_destination',
            },

        ],
    }

);
</script>
@stop
