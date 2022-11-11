@extends('adminlte::page')

@section('title', 'Orders dump Test')

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
    <div class="col-1.5">
        <div style="margin-top: 2.0rem;">
            <h2 class="">
                <h1 class="m-0 text-dark"> Orders Status: &nbsp;</h1>
            </h2>
        </div>
    </div>

    <!-- <form class="row"> -->
    <div class="col-2.5">
        <x-adminlte-select name="ware_id" id="store_select" label="Select Store">
            <option value=" ">Select Store</option>
            @foreach($stores as $store)
            <option value="{{$store->store_id}}">{{$store->store_name}}</option>
            @endforeach
        </x-adminlte-select>
    </div>
    <!-- </form> -->

    <div class="col-9">
        <h6 class="mb-4 text-right">
            <div class="search">
                <label>
                    <div style="margin-top: 1.8rem;">
                        Search:
                        <input type="text" id="myInput" class="d-inline-block" placeholder="Amazon Order ID" autocomplete="off" />
                    </div>
                </label>
            </div>
        </h6>
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
        <tr class="text-bold bg-info">
            <!-- <th>Store Name</th> -->
            <th>Amazon Order ID</th>
            <th>Order Item ID</th>
            <th>Courier Name</th>
            <th>Courier AWB</th>
            <th>Zoho ID</th>
            <th>Zoho Order Id</th>
            <th>Amazon Order Status</th>
            <th>Created Time</th>
        </tr>
    </thead>
    <tbody id="data_display">

    </tbody>
</table>
@stop


@section('js')
<script type="text/javascript">
    /*hide untill selection is done */

    $("#detail_table,.search").hide();
    $("#store_select").on('change', function(e) {
        $("#detail_table,.search").show();
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


    $('#store_select').change(function(e) {

        e.preventDefault();
        var id = $(this).val();

        $.ajax({
            method: 'GET',
            url: '/orders/dump/test',
            data: {
                'id': id,
                "_token": "{{ csrf_token() }}",
            },
            'dataType': 'json',
            success: function(response) {
                console.log(response['data']);

                let html = '';
                if (response.hasOwnProperty("success")) {
                    $.each(response['data'], function(index, value) {

                        const d = new Date(value.created_at);

                        html += "<tr>";
                        html += "<td>" + value.amazon_order_id + "</td>";
                        html += "<td>" + value.order_item_id + "</td>";


                        if (value.courier_name == null) {
                            html += "<td>" + 'NA' + "</td>";
                        } else {
                            html += "<td>" + value.courier_name + "</td>";
                        }

                        if (value.courier_awb == null) {
                            html += "<td>" + 'NA' + "</td>";
                        } else {
                            html += "<td>" + value.courier_awb + "</td>";
                        }

                        if (value.zoho_id == null) {
                            html += "<td>" + 'NA' + "</td>";
                        } else {
                            html += "<td>" + value.zoho_id + "</td>";
                        }

                        if (value.zoho_order_id == null) {
                            html += "<td>" + 'NA' + "</td>";
                        } else {
                            html += "<td>" + value.zoho_order_id + "</td>";
                        }
                        if (value.amzn_temp_order_status == null) {
                            html += "<td>" + 'NA' + "</td>";
                        } else {
                            html += "<td>" + value.amzn_temp_order_status + "</td>";
                        }

                        html += "<td>" + d.toDateString() + "</td>";
                        html += "</tr>";

                    });
                    // html += "<td>" + value.bin + "</td>";

                    $("#data_display").html(html);
                    return true;
                }
                alert("No Data exists");
            },
            error: function(response) {
                console.log(response);

            }
        });

    });
</script>
@stop