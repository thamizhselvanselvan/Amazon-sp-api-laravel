@extends('adminlte::page')

@section('title','Inventory Reports')
@section('css')
<link rel="stylesheet" href="/css/style.css">
@stop

@section('content_header')
<h1 class="m-0 text-dark"> Daily Inventory  Reports</h1>
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
    <div class="col-9">
        <input type="radio" name="size" id="ware">
        <label for="ware"> Warehouse Wise Report</label>
    </div>
    <div class="col-1 justify-content-right">
        <h2>
            <div style="margin-top: -1rem;">
                <x-adminlte-button type="submit" label="Export" theme="primary" icon="fas fa-file-export " id="export" />
            </div>
        </h2>
    </div>
</div>
<div class="row" id="warehouse">
    <div class="col-2">
        <x-adminlte-select name="ware_id" label="Select Warehouse">
            <option value=" ">Select Warehouse</option>
            @foreach ($ware_lists as $ware_list)
            <option value="{{ $ware_list->id }}">{{ $ware_list->name }}</option>
            @endforeach
        </x-adminlte-select>

    </div>
</div>

<table class="table table-bordered yajra-datatable table-striped " id="report_table">
    <thead>
        <tr>
            <td id="detail">Date</td>
            <td id="detail">Opening Stock</td>
            <td id="detail">Open Stock Amount</td>
            <td id="detail">Inwarded</td>
            <td id="detail">Inv.Inwarded Amt</td>
            <td id="detail"> Outwarded</td>
            <td id="detail">Inv.Outwarding Amt</td>
            <td id="detail">Closing Stock</td>
            <td id="detail">Closing Stock Amount</td>
        </tr>
    </thead>
    <tbody id="data_display">
        <tr>
            <td>{{ $data['date'] }}</td>
            <td>{{ $data['open_stock'] }}</td>
            <td>{{ $data['open_stock_amt'] }}</td>
            <td>{{ $data['inwarded'] }}</td>
            <td>{{ $data['tdy_inv_amt'] }}</td>
            <td>{{ $data['outwarded'] }}</td>
            <td>{{ $data['tdy_out_amt'] }}</td>
            <td>{{ $data['closing_stock'] }}</td>
            <td>{{ $data['closing_amt'] }}</td>
         
            
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
                $("#export").hide();

                $("#ware ").on('click', function(e) {
                    $("#warehouse").show();
                });
                $("#warehouse ").on('change', function(e) {
                    $("#report_table").show();
                });
                $("#entire ").on('click', function(e) {
                    $("#warehouse").hide();
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
    
</script>
@stop