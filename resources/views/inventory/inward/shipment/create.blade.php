@extends('adminlte::page')

@section('title', 'Create Shipment')

@section('css')

<link rel="stylesheet" href="/css/styles.css">
@stop
@section('content_header')
<h1 class="m-0 text-dark">Inward Shipment</h1>
@stop
@section('content')
<!-- 
<div class="row">
    <div class="col">
        <a href="{{ route('shipments.index') }}" class="btn btn-primary">
            <i class="fas fa-long-arrow-alt-left btn-sm"></i> Back
        </a>
    </div>
</div> -->
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
        <div class="form-group">
            <x-adminlte-select name="warehouse" label="Select Warehouse:" id="warehouse">
                <option value="0">Select Warehouse</option>
                @foreach ($ware_lists as $ware_list)
                <option value="{{ $ware_list->id }}">{{$ware_list->name }}</option>
                @endforeach
            </x-adminlte-select>
        </div>
    </div>
    <div class="col-2">
        <div class="form-group">
            <x-adminlte-select name="source" label="Select Source:" id="source">
                <option value=" ">Select source</option>
                @foreach ($source_lists as $source_list)
                <option value="{{ $source_list->id }}">{{$source_list->name }}</option>
                @endforeach
            </x-adminlte-select>
        </div>
    </div>
    <div class="col-2">
        <div id="currency">
            <x-adminlte-select name="currency" id="currency_input" label="Currency:">
                <option value="0">Select Currency </option>
                @foreach ($currency_lists as $currency_list)
                <option value="{{ $currency_list->id }}">{{$currency_list->code }}</option>
                @endforeach
            </x-adminlte-select>
            <!-- <x-adminlte-input label="Currency:" id="currency_input" name="currency" type="text" placeholder="Currency" /> -->
        </div>
    </div>
    <div class="col text-right">
        <div style="margin-top: 1.8rem;">
            <!-- //<a href="/shipment/storeshipment"> -->
            <x-adminlte-button label="Create Shipment" theme="primary" icon="fas fa-plus" id="create" class="btn-sm create_shipmtn_btn" />
            <!-- </a> -->

        </div>
    </div>
</div>

<div class="row">
    <div class="col-2" id="asin">
        <div class="form-group">
            <label>Enter ASIN:</label>
            <div class="autocomplete" style="width:400px;">
                <textarea name="upload_asin" rows="20" placeholder="Add Asins here..." id="" type="text" autocomplete="off" class="form-control up_asin"></textarea>
            </div>
        </div>
    </div>
    <div class="col-12">
        <x-adminlte-button label="Submit" theme="primary" icon="fas fa-file-upload" id="upload" class="btn-sm upload_asin_btn" />
    </div>
</div>
</div>
<br>
<table class="table table-bordered yajra-datatable table-striped" id="report_table">
    <thead>
        <tr>
            <td>asin</td>
            <td>Item Name</td>
            <td>Quantity</td>
            <td>Price/Unit</td>
            <td>Action</td>
        </tr>
    </thead>
    <tbody>
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

    /*Hide Fields Untill Selection Is Made:*/
    $("#report_table").hide();
    $("#create").hide();
    $("#asin").hide();
    $("#upload").hide();
    $("#currency").hide();

    $("#source").on('change', function(e) {
        $("#asin").show();
    });

    $("#asin").on('click', function(e) {
        $("#upload").show();
    });

    $("#upload").on('click', function(e) {
        $("#currency,#report_table,#create").show();
    });
    // $("#upload").on('click', function(e) {
    //     $("#asin,#upload").hide();
    // });


    $(".upload_asin_btn").on("click", function() {
        let uploaded = $('.up_asin').val();
        let source = $('#source').val();

        $.ajax({
            method: 'POST',
            url: '/shipment/upload',
            data: {
                'asin': uploaded,
                'source': source,
                "_token": "{{ csrf_token() }}",
            },
            'dataType': 'json',
            success: function(response) {
                console.log(response.data);

                let html = '';
                $.each(response, function(index, value) {

                    let html = "<tr class='table_row'>";
                    html += "<td name='asin[]'>" + response.data[asin] + "</td>";
                    html += "<td name='name[]'>" + response.data[2] + "</td>";
                    html += '<td> <input type="text" value="1" name="quantity[]" id="quantity"> </td>'
                    html += '<td> <input type="text" value="0" name="price[]" id="price"> </td>'
                    html += '<td> <button type="button" id="remove" class="btn btn-danger remove1">Remove</button></td>'
                    html += "</tr>";

                    $("#report_table").append(html);
                });



            },
            error: function(response) {
                console.log(response);
            }
        });
    });

    $(".create_shipmtn_btn").on("click", function() {
        let ware_valid = $('#warehouse').val();
        let currency_valid = $('#currency_input').val();
        if (ware_valid == 0) {
            alert('warehouse field is required');
            return false;
        } else if (currency_valid == 0) {
            alert('currency field is required');
            return false;
        } else {

            let self = $(this);
            let table = $("#report_table tbody tr");
            //let data = {};
            let data = new FormData();

            table.each(function(index, elm) {

                let cnt = 0;
                let td = $(this).find('td');
                //  console.log(td);

                data.append('asin[]', td[0].innerText);
                data.append('name[]', td[1].innerText);
                data.append('quantity[]', td[2].children[0].value);
                data.append('price[]', td[3].children[0].value);

            });

            let source = $('#source').val();
            data.append('source', source);

            let warehouse = $('#warehouse').val();
            data.append('warehouse', warehouse);


            let currency = $('#currency_input').val();
            data.append('currency', currency);

            $.ajax({
                method: 'POST',
                url: '/shipment/storeshipment',
                data: data,
                processData: false,
                contentType: false,
                response: 'json',
                success: function(response) {

                    console.log(response);
                    //alert('success');

                    if (response.success) {
                        getBack();
                    }
                },
                error: function(response) {
                    console.log(response);
                }
            });
        }
    });
    /*Redirect to Index:*/
    function getBack() {
        window.location.href = '/inventory/shipments'
    }


    $(document).ready(function() {

        $("#create_shipmtn_btn").submit(function(e) {

            //stop submitting the form to see the disabled button effect
            e.preventDefault();

            //disable the submit button
            $("#create_shipmtn_btn").attr("disabled", true);

            //disable a normal button
            $("#create_shipmtn_btn").attr("disabled", true);

            return true;

        });
    });


    /* Display Autocomplete data:*/
    // function getData(asin, item_name) {

    //     let html = "<tr class='table_row'>";
    //     html += "<td name='asin[]'>" + asin + "</td>";
    //     html += "<td name='name[]'>" + item_name + "</td>";
    //     html += '<td> <input type="text" value="1" name="quantity[]" id="quantity"> </td>'
    //     html += '<td> <input type="text" value="0" name="price[]" id="price"> </td>'
    //     html += '<td> <button type="button" id="remove" class="btn btn-danger remove1">Remove</button></td>'
    //     html += "</tr>";

    //     $("#report_table").append(html);

    // }

    /*Delete Row :*/
    $('#report_table').on('click', ".remove1", function() {

        $(this).closest("tr").remove();
    });
</script>
@stop