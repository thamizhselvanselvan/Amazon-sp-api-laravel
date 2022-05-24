@extends('adminlte::page')

@section('title', 'Outwardings')

@section('css')

<link rel="stylesheet" href="/css/styles.css">

@stop

@section('content_header')
<h1 class="m-0 text-dark">Inventory Outwarding</h1>
<div class="row">
    <div class="col">
        <a href="{{ route('outwardings.index') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-long-arrow-alt-left"></i> Back
        </a>
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



        <table class="table table-bordered yajra-datatable table-striped">
            <thead>
                <tr>
                    <th> ID</th>
                    <th>Shipment ID</th>
                    <th>Warehouse</th>
                    <th>Destination</th>
                    <th>Currency</th>
                    <th>Items</th>
                    <th>Inwarding Date</th>
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
            ajax: "{{ route('outwarding.view') }}",
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'ship_id',
                    name: 'ship_id'
                },
                {
                    data: 'warehouse_name',
                    name: 'warehouse_name'
                },
                {
                    data: 'destination_name',
                    name: 'destination_name'
                },
                {
                    data: 'currency',
                    name: 'currency'
                },
                {
                    data: 'items',
                    name: 'items'
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                },
            ]
        });
    });
</script>
@stop