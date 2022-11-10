@extends('adminlte::page')

@section('title', 'Notification')

@section('css')

<link rel="stylesheet" href="/css/styles.css">

@stop

@section('content_header')
<h1 class="m-0 text-dark">Shipment Notification</h1>

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
            @if (request('success'))
            <div class="alert alert-success alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{request('success')}}</strong>
            </div>
            @endif
        </div>
        <table class="table table-bordered yajra-datatable table-striped">
            <thead>
                <tr class='text-bold bg-info'>
                    <th>ID</th>
                    <th>Order ID</th>
                    <th>PayloadID</th>
                    <th>Shipment ID</th>
                    <!-- <th>Shipment Type</th> -->
                    <th>Operation</th>
                    <th>Order Date</th>
                    <th>Notice Date</th>
                    <th>Shipment Date</th>
                    <th>Delivery Date</th>
                    <th>Status </th>
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

        $.extend($.fn.dataTable.defaults, {
            pageLength: 50,

        });

        let yajra_table = $('.yajra-datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('business.orders.shipment.list') }}",
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'order_id',
                    name: 'order_id'
                },
                 {
                    data: 'payload',
                    name: 'payload'
                },
                 {
                    data: 'shipment_id',
                    name: 'shipment_id'
                },
                //  {
                //     data: 'shipment_type',
                //     name: 'shipment_type'
                // },
                
                 {
                    data: 'operation',
                    name: 'operation'
                },
                 
                 {
                    data: 'order_date',
                    name: 'order_date'
                },
                 {
                    data: 'notice_date',
                    name: 'notice_date'
                },
                
                 {
                    data: 'shipment_date',
                    name: 'shipment_date'
                },
                 {
                    data: 'delivery_date',
                    name: 'delivery_date'
                },

                 {
                    data: 'status',
                    name: 'status'
                },
                 
            ]
        });

    });
</script>
@stop