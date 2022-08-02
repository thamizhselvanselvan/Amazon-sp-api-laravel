@extends('adminlte::page')

@section('title','Inventory Reports')
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
<h1 class="m-0 text-dark"> Daily Inventory Reports</h1>
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
<div class="row">
    <div class="col-2">
        <input type="radio" name="size" id="entire">
        <label for=" entire"> Entire Warehouse Report</label>
    </div>
    <div class="col-2">
        <input type="radio" name="size" id="ware">
        <label for="ware"> Warehouse Wise Report</label>
    </div>
    <div class="col-7">
        <input type="radio" name="size" id="tag">
        <label for="ware"> Tag Wise Report</label>
    </div>
    <div class="col-1 justify-content-right">
        <form class="row" action="/export/daily">
            <h2>
                <div style="margin-top: -1rem;">
                    <x-adminlte-button type="submit" label="Export" theme="primary" icon="fas fa-file-export " id="export" />
                </div>
            </h2>
        </form>
    </div>
</div>
<div class="row" id="warehouse">
    <div class="col-2">
        <x-adminlte-select name="ware_id" class="demo" label="Select Warehouse">
            <option value=" ">Select Warehouse</option>
            @foreach ($ware_lists as $ware_lists)
            <option value="{{ $ware_lists->id }}">{{ $ware_lists->name }}</option>
            @endforeach
        </x-adminlte-select>

    </div>
</div>
<div class="row" id="tagrep">
    <div class="col-2">
        <x-adminlte-select name="tag" class="tag" label="Select Tag">
            <option value=" ">Select Tag</option>
            @foreach ($tags as $tags)
            <option value="{{ $tags->id }}">{{ $tags->name }}</option>
            @endforeach
        </x-adminlte-select>

    </div>
</div>
<table class="table table-bordered yajra-datatable table-striped " id="table">
    <thead>
        <tr>
            <th id="detail">Date</th>
            <th id="detail">Opening Stock</th>
            <th id="detail">Open Stock Amount</th>
            <th id="detail">Inwarded</th>
            <th id="detail">Inv.Inwarded Amt</th>
            <th id="detail"> Outwarded</th>
            <th id="detail">Inv.Outwarding Amt</th>
            <th id="detail">Closing Stock</th>
            <th id="detail">Closing Stock Amount</th>
        </tr>
    </thead>
    <tbody id="report_table">
        <tr>
            <td>{{ $data['date'] }}</td>
            <td>{{ $data['open_stock'] }}</td>
            <td>&#8377 {{ $data['open_stock_amt'] }}</td>
            <td> {{ $data['inwarded'] }}</td>
            <td> &#8377 {{ $data['tdy_inv_amt'] }}</td>
            <td>{{ $data['outwarded'] }}</td>
            <td> &#8377 {{ $data['tdy_out_amt'] }}</td>
            <td>{{ $data['closing_stock'] }}</td>
            <td> &#8377 {{ $data['closing_amt'] }}</td>
        </tr>

    </tbody>
</table>
@stop
@section('js')

<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $(function() {

        $("#warehouse").hide();
        $("#report_table").hide();
        $("#table").hide();
        $("#export").hide();
        $("#tagrep").hide();

        $("#ware ").on('click', function(e) {
            $("#warehouse").show();
        });
        $("#tag ").on('click', function(e) {
            $("#tagrep").show();
        });
        $("#tag ").on('click', function(e) {
            $("#warehouse").hide();
        });


        $("#ware ").on('click', function(e) {
            $("#tagrep").hide();
        });


        $("#warehouse ").on('change', function(e) {
            $("#table").show();
        });
        $("#warehouse ").on('change', function(e) {
            $("#report_table").show();
        });

        $("#tagrep ").on('change', function(e) {
            $("#table").show();
        });
        $("#tagrep ").on('change', function(e) {
            $("#report_table").show();
        });


        $("#entire ").on('click', function(e) {
            $("#warehouse").hide();
        });
        $("#entire ").on('click', function(e) {
            $("#tagrep").hide();
        });
        $("#entire ").on('click', function(e) {
            $("#table").show();
        });
        $("#ware ").on('click', function(e) {
            $("#report_table").hide();
        });
        $("#entire ").on('click', function(e) {
            $("#report_table").show();
        });
        $("#entire,#warehouse ").on('change', function(e) {
            $("#export").show();
        });

    });

    $('#warehouse').change(function(e) {

        e.preventDefault();
        var id = $('.demo').val();

        $.ajax({
            url: '/inventory/warewise',
            method: 'GET',
            data: {
                'id': id,
                "_token": "{{ csrf_token() }}",
            },
            'dataType': 'json',
            success: function(response) {
                let html = '';

                html += "<tr>";
                html += "<td>" + response.date + "</td>";
                html += "<td>" + response.open_stock + "</td>";
                html += "<td>" + response.open_stock_amt + "</td>";
                html += "<td>" + response.inwarded + "</td>";
                html += "<td>" + response.tdy_inv_amt + "</td>";
                html += "<td>" + response.outwarded + "</td>";
                html += "<td>" + response.tdy_out_amt + "</td>";
                html += "<td>" + response.closing_stock + "</td>";
                html += "<td>" + response.closing_amt + "</td>";
                html += "</tr>";
                $("#report_table").html(html);
            },
            error: function(response) {
                console.log(response);
            }
        });

    });

    $("#tagrep ").on('change', function(e) {
        var val = $('.tag').val();

        $.ajax({
            url: '/inventory/tagwise',
            method: 'GET',
            data: {
                'id': val,
                "_token": "{{ csrf_token() }}",
            },
            'dataType': 'json',
            success: function(response) {
                let html = '';
                // console.log(value);
                html += "<tr>";
                html += "<td>" + response.date + "</td>";
                html += "<td>" + response.open_stock + "</td>";
                html += "<td>" + response.open_stock_amt + "</td>";
                html += "<td>" + response.inwarded + "</td>";
                html += "<td>" + response.tdy_inv_amt + "</td>";
                html += "<td>" + response.outwarded + "</td>";
                html += "<td>" + response.tdy_out_amt + "</td>";
                html += "<td>" + response.closing_stock + "</td>";
                html += "<td>" + response.closing_amt + "</td>";
                html += "</tr>";
                $("#report_table").html(html);
            },
            error: function(response) {
                console.log(response);
            }
        });

    });
</script>
@stop