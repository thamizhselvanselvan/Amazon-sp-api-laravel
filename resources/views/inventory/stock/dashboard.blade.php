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

    #detail {
        font-weight: bold;
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
        <form class="row" action="/inventory/export">
            <div class="col-2">
                <x-adminlte-select name="ware_id" id="warehouse" label="Select Warehouse">
                    <option value=" ">Select Warehouse</option>
                    @foreach ($ware_lists as $ware_list)
                    <option value="{{ $ware_list->warehouses->id }}">{{$ware_list->warehouses->name }}</option>
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

<table class="table table-bordered yajra-datatable table-striped " id="detail_table">
    <thead>
        <tr>
            <td id="detail">Warehouse Name</td>
            <td id="detail">Shipment ID</td>
            <td id="detail">ASIN</td>
            <td id="detail">Item Name</td>
            <td id="detail">Price/Unit</td>
            <td id="detail">Quantity In.</td>
            <td id="detail">Quantity/out.</td>
            <td id="detail">Quantity Left</td>
            <td id="detail">Inwarding Date</td>
            <td id="detail">Bin</td>
        </tr>
    </thead>
    <tbody id="data_display">

    </tbody>
</table>
@stop

@section('js')
<script type="text/javascript">
    /*hide untill data is filled*/
    $("#inward").hide();
    $("#outward,#detail_table,#export").hide();
    $("#warehouse").on('change', function(e) {
        $("#inward,#outward,#detail_table,#export").show();
    });

    $('#warehouse').change(function(e) {

        e.preventDefault();
        var id = $(this).val();

        $.ajax({
            method: 'GET',
            url: '/inventory/list',
            data: {
                'id': id,
                "_token": "{{ csrf_token() }}",
            },
            'dataType': 'json',
            success: function(response) {
                console.log(response);
                let html = '';
                $.each(response, function(index, value) {

                    const d = new Date(value.created_at);

                    html += "<tr>";
                    html += "<td>" + value.warehouses.name + "</td>";
                    html += "<td>" + value.ship_id + "</td>";
                    html += "<td>" + value.asin + "</td>";
                    html += "<td>" + value.item_name + "</td>";
                    html += "<td>" + value.price + "</td>";
                    html += "<td>" + value.quantity + "</td>";
                    html += "<td>" + value.out_quantity + "</td>";
                    html += "<td>" + value.balance_quantity + "</td>";
                    html += "<td>" + d.toDateString() + "</td>";
                    html += "<td>" + value.bin + "</td>";
                    html += "</tr>";

                });


                $("#data_display").html(html);

                // window.location.href = '/inventory/exp/' + id;
                // alert(' pdf Downloaded  successfully');

            },
            error: function(response) {
                console.log(response);
            }
        });

    });

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
                alert('  Downloaded  successfully');


            },
            error: function(response) {
                console.log(response);
            }
        });
    });
</script>
@stop