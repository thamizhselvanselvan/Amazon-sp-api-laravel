@extends('adminlte::page')

@section('title', 'SNT Inwarding')

@section('css')

<link rel="stylesheet" href="/css/styles.css">
@stop
@section('content_header')
<h1 class="m-0 text-dark">Shipntrack sInward Shipment</h1>
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
            <x-adminlte-select name="mode" label="Select Mode:" id="mode">
                <option value="0">Source-Destination</option>
                @foreach ($modes as $mode)
                <option value={{ $mode }}>
                    {{ $mode }}
                </option>
                @endforeach

            </x-adminlte-select>
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

<div class="row d-none remove">
    <div class="col-2 " id="awb">
        <div class="form-group">
            <label>Enter AWB:</label>
            <div class="autocomplete" style="width:400px;">
                <textarea name="upload_awb" rows="5" placeholder="Add AWB's here..." id="awb" type=" text" autocomplete="off" class="form-control up_awb"></textarea>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-0.5 d-none remove">
        <x-adminlte-button label="Submit" theme="primary" icon="fas fa-file-upload" id="upload" class="btn-sm upload_awb_btn" />
    </div>
    <div></div>&nbsp;

    <div></div>&nbsp;
    <div class="col-0.5 d-none remove">
        <x-adminlte-button label="clear" theme="success" icon="fas fa-broom" id="clear" class="btn-sm clear_btn " />
    </div>
</div>


<br>
<table class="table table-bordered yajra-datatable table-striped" id="report_table">
    <thead>
        <tr class="table-info d-none ">
            <th>AWB</th>
            <th>Refrence ID</th>
            <th>Consignor</th>
            <th>Consignee</th>
            <th>Forwarder 1</th>
            <th>Forwarder 2</th>
            <th>Forwarder 3</th>
            <th>Forwarder 4</th>
            <th>Staus</th>
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

    $("#mode").on('change', function(e) {
        $('.remove').removeClass('d-none');
    });


    $("#clear").on('click', function(e) {
        $('.up_awb').val('');
    });

    //submit//
    $(".upload_awb_btn").on("click", function() {
        let uploaded = $('.up_awb').val();
        let mode = $('#mode').val();
       
        if (mode == 0) {
            alert('Mode Required.. please select Mode....');
            return false;
        }
        if (uploaded == '') {
            alert('AWB Required.. please enter AWB....');
            return false;
    }


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
                data.append('proc_price[]', td[6].children[0].value);

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