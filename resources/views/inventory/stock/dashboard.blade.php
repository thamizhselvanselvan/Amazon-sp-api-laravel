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
<div class="row ">
<div style="margin-top: 1.3rem;">
    <div class="col">
        <h2 class="">
            <h1 class="m-0 text-dark"> Stocks :</h1>
        </h2>
    </div>
    </div>
    <div class="col-2">

        <x-adminlte-select name="ware_id" id="warehouse" label="Select Warehouse">
            <option value=" ">Select Warehouse</option>
            @foreach ($ware_lists as $ware_list)
            <option value="{{ (isset($ware_list->shipment->warehouses)) ? $ware_list->shipment->warehouses->id : '' }}">{{ (isset($ware_list->shipment->warehouses)) ? $ware_list->shipment->warehouses->name : '' }}</option>
            @endforeach
        </x-adminlte-select>

    </div>
    <!-- <h2>
        <div style="margin-top: 1.8rem;">
            <a href="{{ route('shipments.create') }}">
                <x-adminlte-button label="Inward" theme="primary" id="inward" icon="fas fa-plus" />
            </a>
            <a href="{{ route('outwardings.create') }}">
                <x-adminlte-button label="Outward" theme="primary" id="outward" icon="fas fa-minus" />
            </a>
        </div>
    </h2> -->

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
            <!-- <td>id </td> -->
            <td id="detail">Warehouse Name</td>
            <td id="detail"> Shipment ID</td>
            <td id="detail">ASIN</td>
            <td id="detail">Item Name</td>
            <td id="detail">Inwarding Price</td>
            <td id="detail">Quantity</td>
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
    $("#outward,#detail_table").hide();
    $("#warehouse").on('change', function(e) {
        $("#inward,#outward,#detail_table").show();
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
                    //  html += "<td>"+value.id+"</td>" ;  
                    html += "<td>" + value.name + "</td>";
                    html += "<td>" + value.ship_id + "</td>";
                    html += "<td>" + value.asin + "</td>";
                    html += "<td>" + value.item_name + "</td>";
                    html += "<td>" + value.price + "</td>";
                    html += "<td>" + value.quantity + "</td>";
                    html += "<td>" + d.toDateString() + "</td>";
                    html += "<td>" + value.bin + "</td>";
                    html += "</tr>";

                });


                $("#data_display").html(html);


            },
            error: function(response) {
                console.log(response);
            }
        });

    });
</script>
@stop