@extends('adminlte::page')

@section('title', 'Inventory Stocks')

@section('css')

<link rel="stylesheet" href="/css/styles.css">

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
            <option value="{{ $ware_list->id }}">{{ (isset($ware_list->warehouses)) ? $ware_list->warehouses->name : '' }}</option>
            @endforeach
        </x-adminlte-select>

    </div>
    <h2>
        <div style="margin-top: 1.8rem;">
            <a href="{{ route('shipments.create') }}">
                <x-adminlte-button label="Inward" theme="primary" id="inward" icon="fas fa-plus" />
            </a>
            <a href="{{ route('outwardings.create') }}">
                <x-adminlte-button label="Outward" theme="primary" id="outward" icon="fas fa-minus" />
            </a>
    </h2>
</div>
</div>
<table class="table table-bordered yajra-datatable table-striped" id="detail_table">
    <thead>
        <tr>
            <td>id </td>
            <td>warehouse Name</td>
            <td>ASIN</td>
            <td>item name</td>
            <td>Inwarding price</td>
            <td>quantity</td>
            <td>Inwarding Date</td>
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
                let html = '';    
                $.each(response, function(index, value) {
                     html += "<tr>";
                     html += "<td>"+value.id+"</td>" ;  
                     html += "<td>"+value.name+"</td>" ;  
                     html += "<td>"+value.asin+"</td>" ;  
                     html += "<td>"+value.item_name+"</td>" ;  
                     html += "<td>"+value.price+"</td>" ;  
                     html += "<td>"+value.quantity+"</td>" ;  
                     html += "<td>"+value.created_at+"</td>" ;  
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