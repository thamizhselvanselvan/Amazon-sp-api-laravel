@extends('adminlte::page')

@section('title', 'Create Shipment')

@section('css')

<link rel="stylesheet" href="/css/styles.css">
@stop
@section('content_header')
<h1 class="m-0 text-dark">Inward Shipment</h1>
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
            <x-adminlte-select name="source" label="Select Source:" id="source1">
                <option value="0">Select source</option>
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
                <textarea name="upload_asin" rows="5" placeholder="Add Asins here..." id="" type=" text" autocomplete="off" class="form-control up_asin"></textarea>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-0.5">
        <x-adminlte-button label="Submit" theme="primary" icon="fas fa-file-upload" id="upload" class="btn-sm upload_asin_btn" />
    </div>
    <div></div>&nbsp;
    <div class="col-0.5">
        <x-adminlte-button label="Refresh" theme="primary" icon="fas fa-redo-alt" id="refresh" class="btn-sm refresh_btn " />
    </div>
    <div></div>&nbsp;
    <div class="col-0.5">
        <x-adminlte-button label="clear" theme="success" icon="fas fa-broom" id="clear" class="btn-sm clear_btn " />
    </div>
</div>


<br>
<table class="table table-bordered yajra-datatable table-striped" id="report_table">
    <thead>
        <tr class="table-info">
            <th>ASIN</th>
            <th>Item Name</th>
            <th>Source</th>
            <th>Tag</th>
            <th>Quantity</th>
            <th>Price/Unit</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody id="report_table_body">
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
    $("#clear").hide();
    $("#currency").hide();
    $("#refresh").hide();

    $("#source1").on('change', function(e) {
        $("#asin").show();
    });

    $("#asin").on('click', function(e) {
        $("#upload,#refresh,#clear").show();
    });

    $("#upload").on('click', function(e) {
        $("#currency,#report_table,#create").show();
    });

    $("#clear").on('click', function(e) {
        $('.up_asin').val('');
    });

    //submit//
    $(".upload_asin_btn").on("click", function() {
        let uploaded = $('.up_asin').val();

        if ((uploaded.length < 10 || upload.lenght > 10)) {
            alert('Invalid Asin');
            return false;
        }
        let source = $('#source1').val();


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
                let table = $("#report_table_body");
                table.append(existing_data(response, source));
            },
            error: function(response) {
                console.log(response);
                alert('Something went Wrong...');
            }
        });
    });

    function existing_data(response) {
        let html = '';
        let source = $('#source1').val();
        // let source_name = $("#source1 option:selected").text();

        $.each(response.data, function(index, value) {

            let asin = value[0].asin;
            let item_name = value[0].item_name;

            if (value == "NA") {
                asin = index;
                item_name = "We are fetching the data in sometime refresh it again";
            }
            let item_class = 'item_' + asin;
            html += "<tr class='table_row'>";
            html += "<td name='asin[]'>" + asin + "</td>";
            html += "<td name='name[]' class='" + item_class + "'>" + item_name + "</td>";
            html += "<td name='source[]'>" + source + "</td>";
            html += `<td>
                     <x-adminlte-select name="tag[]" class="tags" id="tag">>
                      <option value="0">Select Tag</option>
                       @foreach ($tags as $tag)
                       <option value="{{ $tag->id }}">{{$tag->name }}</option>
                      @endforeach
                    </x-adminlte-select>
                     </td>`

            html += '<td><input type="text" value="1" name="quantity[]" id="quantity">  </td>'
            html += '<td> <input type="text" value="0" name="price[]" id="price"> </td>'
            html += '<td> <button type="button" id="remove" class="btn btn-sm btn-danger remove1">Remove</button></td>'
            html += "</tr>";
        });

        return html;
    }

    //refresh//

    $(".refresh_btn").on("click", function() {
        let uploaded = $('.up_asin').val();

        var result = '';
        $("#report_table tbody tr").each(function() {
            let restasin = $(this).find("td:first").html();
            result += restasin + ",";
        });

        let source = $('#source1').val();
        if ((uploaded.length < 10)) {
            alert('Invalid Asin');
            return false;
        }

        $.ajax({
            method: 'POST',
            url: '/shipment/upload/refresh',
            data: {
                'asin': result,
                'source': source,
                "_token": "{{ csrf_token() }}",
            },
            'dataType': 'json',
            success: function(response) {
                console.log(response)
                let first_time = 1;
                let table = $("#report_table_body");

                ref_existing_data(response);

            },
            error: function(response) {
                console.log(response)
                alert('error');
            }
        });
    });

    function ref_existing_data(response) {
        let html = '';
        $.each(response.data, function(index, value) {
            let asin = value[0].asin;
            let id = '.item_' + asin;
            let title = value[0].item_name;
            $(id).text(title);
        });
        return html;
    }

    //create Shipment//
    $(".create_shipmtn_btn").on("click", function() {
        $(this).prop('disabled', true);
        let ware_valid = $('#warehouse').val();
        let source = $('#source1').val();
        let currency_valid = $('#currency_input').val();
        let validation = true;
        // let tag_valid = $('.tags').val();
        if (ware_valid == 0) {
            alert('warehouse field is required');
            validation = false;
            $('.create_shipmtn_btn').prop('disabled', false);
            return false;
        } else if (currency_valid == 0) {
            $('.create_shipmtn_btn').prop('disabled', false);
            alert('currency field is required');
            validation = false;
            return false;
        } else if (source == '0') {
            $('.create_shipmtn_btn').prop('disabled', false);
            alert('Source field is required');
            validation = false;
            return false;
        } else {

            let self = $(this);
            let table = $("#report_table tbody tr");
            let data = new FormData();
            table.each(function(index, elm) {

                let cnt = 0;
                let td = $(this).find('td');

                let tag = $(td[3]).find('select').val();
                if (tag == 0) {
                    $('.create_shipmtn_btn').prop('disabled', false);
                    alert('please select the Tag for all ASIN');
                    validation = false;
                    return false;
                }

                data.append('asin[]', td[0].innerText);
                data.append('name[]', td[1].innerText);
                data.append('source[]', td[2].innerText);
                data.append('tag[]', $(td[3]).find('select').val());
                data.append('quantity[]', td[4].children[0].value);
                data.append('price[]', td[5].children[0].value);

            });

            // let source = $('#source1').val();
            // data.append('source', source);

            let warehouse = $('#warehouse').val();
            data.append('warehouse', warehouse);


            let currency = $('#currency_input').val();
            data.append('currency', currency);
            if (validation) {
                $.ajax({
                    method: 'POST',
                    url: '/shipment/storeshipment',
                    data: data,
                    processData: false,
                    contentType: false,
                    response: 'json',
                    success: function(response) {
                        // $('.create_shipmtn_btn').prop('disabled', false);
                        if (response.success) {
                            getBack();
                        }
                    },
                    error: function(response) {
                        // console.log(response);
                        alert('Something Went Wrong.. Try creating shipment Again');
                        $('.create_shipmtn_btn').prop('disabled', false);
                    }
                });
            }

        }
    });

    //*Redirect to Index:*//
    function getBack() {
        window.location.href = '/inventory/shipments?success=Shipment has been created successfully'
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

    /*Delete Row :*/
    $('#report_table').on('click', ".remove1", function() {

        $(this).closest("tr").remove();
    });
</script>
@stop