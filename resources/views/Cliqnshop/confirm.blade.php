@extends('adminlte::page')

@section('title', 'Confirmation')

@section('css')

<link rel="stylesheet" href="/css/styles.css">

@stop

@section('content_header')
<h1 class="m-0 text-dark">Order's Confirmation</h1>

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
                    <th>Order Date</th>
                    <th>Received  ID</th>
                    <th>confirm ID</th>
                    <!-- <th>Shipment Type</th> -->
                    <th>Notice Date</th>
                    <th>Amount</th>
                    <th>Tax</th>
                    <th>Shipping Amount</th>
                    <th>Total Amount</th>
                    <th>Quantity</th>
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
            ajax: "{{ route('business.orders.confirm.list') }}",
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
                    data: 'order_date',
                    name: 'order_date'
                },
                 {
                    data: 'payload',
                    name: 'payload'
                },
                 {
                    data: 'confirm_id',
                    name: 'confirm_id'
                },
                //  {
                //     data: 'shipment_type',
                //     name: 'shipment_type'
                // },
                 {
                    data: 'notice_date',
                    name: 'notice_date'
                },
                 {
                    data: 'amount',
                    name: 'amount'
                },
                 {
                    data: 'tax',
                    name: 'tax'
                },
                 {
                    data: 'shipping_amount',
                    name: 'shipping_amount'
                },
                 {
                    data: 'total_amount',
                    name: 'total_amount'
                },
             
                 {
                    data: 'quantity',
                    name: 'quantity'
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