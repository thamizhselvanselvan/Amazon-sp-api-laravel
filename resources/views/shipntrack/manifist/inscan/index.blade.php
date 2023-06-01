@extends('adminlte::page')

@section('title', 'SNT In-Scan')

@section('css')

<link rel="stylesheet" href="/css/styles.css">
<style>
    .table td {
        padding: 0;
        padding-left: 5px;
    }

    .table th {
        padding: 2;
        padding-left: 5px;
    }
</style>
@stop

@section('content_header')
<div class="row">
    <div class="col-2">
        <div style="margin-top:-0.1rem">
            <a href="{{route('shipntrack.inscan.view')}}">
                <x-adminlte-button label="Create Manifist" class="btn-sm" theme="primary" icon="fas fa-plus" />
            </a>
        </div>
    </div>
    <div class="col-2"></div>
    <div class="col-6">

        <h1 class="m-0 text-dark"> SNT In-Scan Shipment</h1>
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
            @if($message = Session::get('error'))
            <div class=" alert alert-danger alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $message }}</strong>
            </div>
            @endif

            <div class="alert_display success">
                @if (request('success'))
                <div class="alert alert-success alert-block">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <strong>{{request('success')}}</strong>
                </div>
                @endif
            </div>

        </div>

        <div id="showTable" class="">
            <table class='table table-bordered yajra-datatable table-striped text-center'>
                <thead>
                    <tr class="table-info">
                        <th>ID</th>
                        <th>Manifest ID</th>
                        <th>Mode</th>
                        <th>Outward Type</th>
                        <th>AWB number </th>
                        <th>Status</th>
                        <!-- <th>Action</th> -->
                    </tr>
                </thead>
                <tbody id='checkTable'>
                </tbody>
            </table>
        </div>


    </div>
</div>
@stop


@section('js')
<script type="text/javascript">
    $(function() {

        $.extend($.fn.dataTable.defaults, {
            pageLength: 100,
        });

        let yajra_table = $('.yajra-datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('shipntrack.inward') }}",
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'manifest_id',
                    name: 'manifest_id'
                },
                {
                    data: 'mode',
                    name: 'mode'
                },
                {
                    data: 'type',
                    name: 'type'
                },
                {
                    data: 'awb_number',
                    name: 'awb_number'
                },
                {
                    data: 'status',
                    name: 'status'
                },

                // {
                //     data: 'action',
                //     name: 'action',
                //     orderable: false,
                //     searchable: false
                // },
            ]
        });

    });
</script>
@stop