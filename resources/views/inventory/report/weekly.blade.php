@extends('adminlte::page')

@section('title','Inventory Reports')
@section('css')
<link rel="stylesheet" href="/css/style.css">
@stop

@section('content_header')
<h1 class="m-0 text-dark"> Weekly Inventory  Reports</h1>
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
    <form class="row" action="/export/weekly">
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
    <tbody >
    <tbody id="data_display">
            @foreach ($week_data as $key => $value)
        <tr>
            @foreach ($value as $data )
            <td>{{$data}}</td>
            @endforeach
        </tr>
        @endforeach
    </tbody>
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