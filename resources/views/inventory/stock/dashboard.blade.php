@extends('adminlte::page')

@section('title', 'Inventory Stocks')

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
    <div class="col-1">
        <div style="margin-top: 1.3rem;">
            <h2 class="">
                <h1 class="m-0 text-dark"> Stocks :</h1>
            </h2>
        </div>
    </div>
    <div class="col-11">
        <!-- <form class="row" action="/inventory/export"> -->
        <form class="row">
            <div class="col-2">
                <x-adminlte-select name="ware_id" id="warehouse" label="Select Warehouse">
                    <option value="">Select Warehouse</option>
                    @foreach ($ware_lists as $ware_list)
                    <option value="{{ $ware_list->warehouses->id }}" {{$request_ware_id ==  $ware_list->warehouses->id  ? "selected" : ''}}>{{$ware_list->warehouses->name }}</option>

                    @endforeach
                </x-adminlte-select>
            </div>

            <div class="col-3">
                <h2>
                    <div style="margin-top: 1.8rem;">
                        <x-adminlte-button type="button" label="Export" theme="primary" icon="fas fa-file-export" id="export" />
                    </div>
                </h2>
            </div>
            </h6>
        </form>
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
    </div>
</div>

<table class="table table-bordered yajra-datatable table-striped" id="detail_table">
    <thead>
        <tr class="table-info">
            <th>ID</th>
            <th>Inventory ID</th>
            <th>Warehouse Name</th>
            <th>Shipment ID</th>
            <th id='asin'>ASIN</th>
            <th>Item Name</th>
            <th>Price/Unit</th>
            <th>Quantity In.</th>
            <th>Quantity out.</th>
            <th>Quantity Left</th>
            <th>Storage Shelve</th>
            <th>Inwarding Date</th>
        </tr>
    </thead>
    <tbody id="data_display">

    </tbody>
</table>
@stop

@section('js')
<script type="text/javascript">
    $(document).ready(function() {
        if ($('#warehouse').val() == '') {
            $('#export').hide();
        }
    });


    $('#warehouse').on('change', function() {
        window.location = "/inventory/stocks/list/" + $(this).val();
    });


    $.extend($.fn.dataTable.defaults, {
        pageLength: 50,
    });



    let yajra_table = $('.yajra-datatable').DataTable({
        processing: true,
        serverSide: true,

        ajax: {
            url: "{{ url($url) }}",
            type: 'get',
            headers: {
                'content-type': 'application/x-www-form-urlencoded',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: function(d) {
                d.ware_id = $('#warehouse').val();
            },
        },
        columns: [{
                data: 'id',
                name: 'id'
            },
            {
                data: 'inventory_id',
                name: 'inventory_id',

            },
            {
                data: 'w_name',
                name: 'w_name',

            },
            {
                data: 'ship_id',
                name: 'ship_id',

            },
            {
                data: 'asin',
                name: 'asin',

            },
            {
                data: 'item_name',
                name: 'item_name'
            },
            {
                data: 'price',
                name: 'price',
                orderable: false,
                searchable: false
            },
            {
                data: 'quantity',
                name: 'quantity',
                orderable: false,
                searchable: false
            },
            {
                data: 'out_quantity',
                name: 'out_quantity',
                orderable: false,
                searchable: false
            },
            {
                data: 'balance_quantity',
                name: 'balance_quantity',
                orderable: false,
                searchable: false
            },
            {
                data: 'bin',
                name: 'bin',
            },
            {
                data: 'created_at',
                name: 'created_at',
                orderable: false,
                searchable: false

            }
        ]
    });



    // $('#warehouse').change(function(e) {

    //     e.preventDefault();
    //     var id = $(this).val();

    //     $.ajax({
    //         method: 'GET',
    //         url: '/inventory/list',
    //         data: {
    //             'id': id,
    //             "_token": "{{ csrf_token() }}",
    //         },
    //         'dataType': 'json',
    //         success: function(response) {
    //             // console.log(response);

    //             let html = '';
    //             $.each(response, function(index, value) {

    //                 const d = new Date(value.created_at);

    //                 html += "<tr>";
    //                 html += "<td>" + value.warehouses.name + "</td>";
    //                 html += "<td>" + value.ship_id + "</td>";
    //                 html += "<td>" + value.asin + "</td>";
    //                 html += "<td>" + value.item_name + "</td>";
    //                 html += "<td>" + value.price + "</td>";
    //                 html += "<td>" + value.quantity + "</td>";

    //                 if (value.out_quantity == null) {
    //                     html += "<td>" + '0' + "</td>";
    //                 } else {
    //                     html += "<td>" + value.out_quantity + "</td>";

    //                 }
    //                 html += "<td>" + value.balance_quantity + "</td>";
    //                 if (value.shelves == null) {
    //                     html += "<td>" + 'Not Allocated' + "</td>"
    //                 } else {
    //                     html += "<td>" + value.shelves.rack_id + '-' + value.shelves.shelve_id + "</td>";
    //                 }
    //                 html += "<td>" + d.toDateString() + "</td>";
    //                 html += "</tr>";

    //             });
    //             // html += "<td>" + value.bin + "</td>";

    //             $("#data_display").html(html);

    //             // window.location.href = '/inventory/exp/' + id;
    //             // alert(' pdf Downloaded  successfully');

    //         },
    //         error: function(response) {
    //             // console.log(response);
    //             alert('error');
    //         }
    //     });

    // });





    /*download PDF */
    $('#export').click(function() {


        var id = $('#warehouse').val();

        $.ajax({
            url: '/inventory/expo',
            method: 'get',
            data: {
                'id': id,
                "_token": "{{ csrf_token() }}",
            },
            success: function(result) {
                window.location.href = '/inventory/exp/' + id;
                alert(' Downloaded successfully');

            },
            error: function(response) {
                // console.log(response);
                alert('error');
            }
        });
    });
</script>
@stop