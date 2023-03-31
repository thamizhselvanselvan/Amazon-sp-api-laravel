@extends('adminlte::page')

@section('title', 'Cliqnshop Orders')

@section('content_header')
@stop


@section('content')

{{-- tabs switcher ~start --}}
<div class="row  pt-4 justify-content-center">    
    <a class="btn btn-lg btn-app bg-secondary" style="width:130px" href ="{{url('business/orders/details')}}">        
        <i class="fa fa-clock-o"></i> Order Pending
    </a>
    <a class="btn btn-lg btn-app bg-success" style="width:130px" href ="{{url('business/booked/details')}}">        
        <i class="fa fa-check"></i> Order booked
    </a>
    <a class="btn btn-lg btn-app bg-info" style="width:130px" href ="{{url('business/orders/confirm')}}">        
        <i class="fa fa-check-circle-o"></i> Order Confirmation
    </a>
    <a class="btn btn-lg btn-app bg-warning" style="width:130px" href ="{{url('business/ship/confirmation')}}">        
        <i class="fa fa-bell "></i> shipment Notification
    </a>      
</div>
{{-- tabs switcher ~end --}}

<div class="loader d-none">
    <div class="sub-loader position-relative ">
        <div class="lds-hourglass"></div>
        <p>Loading...</p>
    </div>
</div>


<div class="row ">
    <div class="col ">
        <div style="margin: 0.1rem 0; text-align: center" >
            <h3>Cliqnshop Booked Orders</h3>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-8">
        @if(session()->has('success'))
        <x-adminlte-alert theme="success" title="Success" dismissable>
            {{ session()->get('success') }}
        </x-adminlte-alert>
        @endif

        @if(session()->has('error'))
        <x-adminlte-alert theme="danger" title="Error" dismissable>
            {{ session()->get('error') }}
        </x-adminlte-alert>
        @endif
    </div>
</div>

<table class="table table-bordered yajra-datatable table-striped" id='orderstable'>
    <thead>
        <tr class='text-bold bg-info'>
            <th>ID</th>
            <th>Order ID</th>
            <th>Sent Payload</th>
            <th>ASIN</th>
            <th>Item Name</th>
            <!-- <th>Price</th> -->
            <th>Quantity</th>
            <th>order Date</th>
            <th>Responce Payload</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody id="data_display">

    </tbody>
</table>
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
            ajax: "{{ route('business.orders.booked.list') }}",
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
                    data: 'sent_payload',
                    name: 'sent_payload'
                },
                {
                    data: 'asin',
                    name: 'asin'
                },
                {
                    data: 'item_name',
                    name: 'item_name'
                },
                // {
                //     data: 'price',
                //     name: 'price'
                // },
                {
                    data: 'quantity',
                    name: 'quantity'
                },

                {
                    data: 'order_date',
                    name: 'order_date'
                },
                {
                    data: 'responce_payload',
                    name: 'responce_payload'
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