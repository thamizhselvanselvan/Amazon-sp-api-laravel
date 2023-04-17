@extends('adminlte::page')

@section('title', 'SNT Inwarding')

@section('css')

<link rel="stylesheet" href="/css/styles.css">
@stop
@section('content_header')
<h1 class="m-0 text-dark">Shipntrack Inward Shipment</h1>
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
            <x-adminlte-select name="mode" label="Source-Destination" id="mode">
                <option value="0">Source-Destination</option>
                @foreach ($destinations as $destination)
                <option value={{ $destination['destination'] }}>
                    {{ $destination['source'] . '-' . $destination['destination'] }}
                </option>
                @endforeach
            </x-adminlte-select>
        </div>
    </div>

    <div class="col text-right">
        <div style="margin-top: 1.8rem;">
            <!-- //<a href="/shipment/storeshipment"> -->
            <x-adminlte-button label="Create Shipment" theme="primary" icon="fas fa-plus" id="create" class="btn-sm d-none create_shipmtn_btn" />
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
        <tr class="table-info table d-none ">
            <th>AWB</th>
            <th>Refrence ID</th>
            <th>Consignor</th>
            <th>Consignee</th>
            <th>Forwarder 1</th>
            <th>Forwarder 2</th>
            <th>Forwarder 3</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody id="table_body">
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
            method: 'get',
            url: "{{route('shipntrack.inward.create')}}",
            data: {
                'awb': uploaded,
                'mode': mode,
                "_token": "{{ csrf_token() }}",
            },
            'dataType': 'json',
            success: function(response) {
                $('.table').removeClass('d-none')
                $('.create_shipmtn_btn').removeClass('d-none')
                let table = $("#table_body");
                table.append(append_data(response));
            },
            error: function(response) {
                console.log(response);
                alert('Something went Wrong...');
            }
        });
    });

    //Autofill AWB data
    function append_data(response) {
        let html = '';
        $.each(response, function(index, value) {
            html += "<tr class='table_row'>";
            html += "<td name='awb_number[]'>" + value.awb_number + "</td>";
            html += "<td name='reference_id []'>" + value.reference_id + "</td>";
            html += "<td name='consignee[]' >" + value.consignee + "</td>";
            html += "<td name='consignor[]'>" + value.consignor + "</td>";
            html += "<td name='courier_partner1[]'>" + value.courier_partner1.user_name + "</td>";

            if (value.courier_partner2 != null) {

                html += "<td name='courier_partner2[]'>" + value.courier_partner2.user_name + "</td>";
            } else {
                html += "<td name='courier_partner2[]'>" + 'NA' + "</td>";
            }
            if (value.courier_partner3 != null) {
                html += "<td name='courier_partner3[]'>" + value.courier_partner3.user_name + "</td>";
            } else {
                html += "<td name='courier_partner3[]'>" + 'NA' + "</td>";
            }

            html += '<td><input type="text-area" class="w-100" value="Receved At Source Warehouse.." name="status[]" id="status">  </td>'
            html += '<td> <button type="button" id="remove" class="btn btn-sm btn-danger remove1">Remove</button></td>'
            html += "</tr>";
        });

        return html;
    }

    //create Shipment//
    $(".create_shipmtn_btn").on("click", function() {
        $(this).prop('disabled', true);
        let mode = $('#mode').val();
        let validation = true;

        if (mode == 0) {
            alert('Mode  is required');
            validation = false;
            $('.create_shipmtn_btn').prop('disabled', false);
            return false;
        } else {

            let self = $(this);
            let table = $("#report_table tbody tr");
            let data = new FormData();
            table.each(function(index, elm) {
                let td = $(this).find('td');

                data.append('awb[]', td[0].innerText);
                data.append('refrence_id[]', td[1].innerText);
                data.append('status[]', td[7].children[0].value);

            });

            data.append('mode', mode);

            if (validation) {
                $.ajax({
                    method: 'POST',
                    url: "{{route('shipntrack.inward.store')}}",
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
        window.location.href = '/shipntrack/inward?success=Shipment has been created successfully'
    }

    /*Delete Row :*/
    $('#report_table').on('click', ".remove1", function() {
        $(this).closest("tr").remove();
    });
</script>
@stop