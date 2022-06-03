@extends('adminlte::page')

@section('title','Inventory Reports')
@section('css')
<link rel="stylesheet" href="/css/style.css">
@stop

@section('content_header')
<h1 class="m-0 text-dark"> Stocks</h1>
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
        <x-adminlte-select name="ware_id" id="warehouse" label="Select Warehouse">
            <option value=" ">Select Warehouse</option>
            @foreach ($ware_lists as $ware_list)
            <option value="{{ $ware_list->warehouse_id }}">{{ (isset($ware_list->warehouses)) ? $ware_list->warehouses->name : '' }}</option>
            @endforeach
        </x-adminlte-select>

    </div>
</div>

<div class="container-fluid">

<div class="row">
    <div class="col-3 ">
        <h4 style="font-weight: bold; text-align: center;">Today </h4>
        <div class="info-box bg-info text-center">
            <div class="info-box-content">
                
                <h5>Total Inwarding</h5>
            </div>
        </div>
    </div>
    <div class="col-3 ">
        <h4 style="font-weight: bold; text-align: center;">Yesterday </h4>
        <div class="info-box bg-info text-center">
            <div class="info-box-content">
              
                <h5>Total Inwarding</h5>
            </div>
        </div>
    </div>
    <div class="col-3 ">
        <h4 style="font-weight: bold; text-align: center;">Last 7 Days </h4>
        <div class="info-box bg-info text-center">
            <div class="info-box-content">
             
                <h5> Total Inwarding</h5>
            </div>
        </div>
    </div>
    <div class="col-3 ">
        <h4 style="font-weight: bold; text-align: center;">Last 30 Days </h4>
        <div class="info-box bg-info  text-center">
            <div class="info-box-content">
               
                <h5>Total Inwarding</h5>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col">
        <div class="info-box bg-success text-center">
            <div class="info-box-content">
                
                <h5>Total Outwarding</h5>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="info-box bg-success text-center">
            <div class="info-box-content">
               
                <h5>Total Outwarding </h5>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="info-box bg-success text-center">
            <div class="info-box-content">
               
                <h5> Total Outwarding</h5>
            </div>
        </div>
    </div>
    <div class="col">

        <div class="info-box bg-success text-center">
            <div class="info-box-content">
         
                <h5>Total Outwarding</h5>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col">
        <div class="info-box bg-warning text-center">
            <div class="info-box-content">
             
                <h5>Total Stocks</h5>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="info-box bg-warning text-center">
            <div class="info-box-content">
              
                <h5>Total Stocks</<h5>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="info-box bg-warning text-center">
            <div class="info-box-content">
               
                <h5>Total Stocks </<h5>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="info-box bg-warning text-center">
            <div class="info-box-content">
               
                <h5>Total Stocks</<h5>
            </div>
        </div>
    </div>
</div>
<!-- 
<div class="row">
    <div class="col">
        <div class="info-box bg-danger text-center">
            <div class="info-box-content">
                
                <h5>5</h5>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="info-box bg-danger text-center">
            <div class="info-box-content">
              
                <h5>5</5>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="info-box bg-danger text-center">
            <div class="info-box-content">
                
                <h5>5</h5>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="info-box bg-danger text-center">
            <div class="info-box-content">
                
                <h5>5</5>
            </div>
        </div>
    </div>
</div>-->
</div> 

@stop

@section('js')
<script type="text/javascript">

    $('#warehouse').change(function(e) {
        e.preventDefault();
        var id = $(this).val();
// alert(id);
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
            },
            error: function(response) {
                console.log(response);
            }
        });

    });
</script>
@stop