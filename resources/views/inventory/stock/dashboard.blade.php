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
            <h6 class="mb-4 text-right col">
                <div class="search">
                    <label>
                        <div style="margin-top: 1.8rem;">
                            Search:
                            <input type="text" id="myInput" class="d-inline-block" placeholder="search asin" autocomplete="off" />
                    </label>
                </div>
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

<table class="table table-bordered yajra-datatable table-striped " id="detail_table">
    <thead>
        <tr>
            <th>Warehouse Name</th>
            <th>Shipment ID</th>
            <th id='asin'>ASIN</th>
            <th>Item Name</th>
            <th>Price/Unit</th>
            <th>Quantity In.</th>
            <th>Quantity/out.</th>
            <th>Quantity Left</th>
            <th>Storage Location</th>
            <th>Inwarding Date</th>
        </tr>
    </thead>
    <tbody id="data_display">

    </tbody>
</table>
@stop

@section('js')
<script type="text/javascript">
    /*hide untill selection is done */

    $("#detail_table,#export,.search").hide();
    $("#warehouse").on('change', function(e) {
        $("#detail_table,#export,.search").show();
    });

    /* Search function */

    $(document).ready(function() {
        $("#myInput").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#data_display tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
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
                    if (value.bins == null) {
                        html += "<td>" + 'Not Allocated' + "</td>"
                    } else {
                        html += "<td>" + value.bins.rack_id  + '-' + value.bins.shelve_id  + '-' + value.bins.bin_id  + "</td>";
                    }

                    html += "<td>" + d.toDateString() + "</td>";
                    html += "</tr>";

                });
                // html += "<td>" + value.bin + "</td>";

                $("#data_display").html(html);

                // window.location.href = '/inventory/exp/' + id;
                // alert(' pdf Downloaded  successfully');

            },
            error: function(response) {
                // console.log(response);
                alert('error');
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