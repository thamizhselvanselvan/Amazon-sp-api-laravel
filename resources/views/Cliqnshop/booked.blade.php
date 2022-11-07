@extends('adminlte::page')

@section('title', 'Cliqnshop Orders')

@section('content_header')

<div class="row">
    <h3>Cliqnshop Booked Orders</h3>

</div>
@stop


@section('content')

<div class="loader d-none">
    <div class="sub-loader position-relative ">
        <div class="lds-hourglass"></div>
        <p>Loading...</p>
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
            <th>ASIN</th>
            <th>Item Name</th>
            <th>Price</th>
            <th>Quantity</th>
            <th> Status</th>
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
                    data: 'prodcode',
                    name: 'prodcode'
                },
            
                 {
                    data: 'name',
                    name: 'name'
                },
                 {
                    data: 'quantity',
                    name: 'quantity'
                },
               
                 {
                    data: 'price',
                    name: 'price'
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