@extends('adminlte::page')

@section('title', 'SNT Outwardings')

@section('css')
<style>
    #checkbox {
        width: 2rem;
        height: 1rem;
    }
</style>
<link rel="stylesheet" href="/css/styles.css">
@stop
@section('content_header')
<div class="row">
    <!-- <div class="col-0.5">
        <a href="{{route('shipntrack.outward')}}">
            <x-adminlte-button label="Back" class="btn-sm" theme="primary" icon="fas fa-arrow-left" />
        </a>
    </div> -->
    <div class="col text-center">
        <h1 class="m-0 text-dark">SNT Outwardings</h1>
    </div>
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
        <div class="alert_display success">
            @if (request('success'))
            <div class="alert alert-success alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{request('success')}}</strong>
            </div>
            @endif
        </div>

    </div>
</div>
<div class="row  align-items-center">
    <div class="col-2">
        <div class="form-group">
            <x-adminlte-select name="mode" label="Source-Destination" id="mode">
                <option value="0">Source-Destination</option>
                @foreach ($destinations as $destination)
                <option value={{$destination['id']}}_{{$destination['destination']}}_{{$destination['process_id']}}>
                    {{ $destination['source'] . '-' . $destination['destination'] }}
                </option>
                @endforeach
            </x-adminlte-select>
        </div>
    </div>
    <div class="col-2">
        <div class="form-group awb type d-none">
            <x-adminlte-input label='Enter AWB Number:' type='text' name='awb' id="awb" placeholder='Enter AWB here..' required />
        </div>
    </div>

    <div id="collapsesix" class="show  col-4 d-none">
        <div class="py-1 d-flex">
            <div class="col-6">
                <x-adminlte-select label="Forwarder 2:" name="forwarder2" id="forwarder_info_2" required>
                    <option value='0'> Forwarder 2</option>
                </x-adminlte-select>
            </div>

            <div class="col-6">
                <x-adminlte-input label="Forwarder 2 AWB :" name="forwarder_2_awb" type="text" placeholder="Forwarder 2 AWB" id="forwarder_2_awb" required />
            </div>
        </div>
    </div>


    <div class="col">
        <div style="margin-top: 1rem;">
            <x-adminlte-button label="Save" theme="primary" icon="fas fa-save" id="create" class=" d-none create_shipmtn_btn" />
        </div>
    </div>
</div>

<br>
<table class="table table-bordered yajra-datatable table-striped d-none" id="report_table">
    <thead>
        <tr class="table-info table  ">
            <th>Consignor</th>
            <th>Consignee</th>
            <th>AWB Number</th>
            <th>Order ID</th>
            <th>Item Name</th>
            <th>Purchase Tracking ID</th>
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
        $('.awb').removeClass('d-none');
    });


    $(document).on("focusout", "#awb", function(e) {
        e.stopPropagation();

        let mode = $('#mode').val();
        let validation = true;
        if (mode == 0) {
            alert('Mode Required.. please select Mode....');
            validation = false;
            return false;
        }
        let data = $('#awb').val();
        if (validation) {
            $.ajax({
                method: 'get',
                url: "{{route('shipntrack.outward.get')}}",
                data: {
                    'awb': data,
                    'mode': mode,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(result) {
                    if (result.hasOwnProperty('error')) {
                        alert(result.error);
                        return false;
                    }
                    let table = $("#table_body");
                    table.append(append_data(result))
                    // $('#awb').val('');
                    $('.show').removeClass('d-none');
                    forwarder();
                },
                error: function() {
                    alert('Invalid ID or No Data Found..');
                }
            });
        }
    });

    function forwarder() {

        let mode = $('#mode').val();
        let split_data = mode.split("_");
        let destination = split_data[1];

        if (destination != 'NULL') {
            $.ajax({
                method: 'get',
                url: "{{ route('shipntrack.forwarder.select.view') }}",
                data: {
                    'destination': destination,

                    "_token": "{{ csrf_token() }}",
                },
                'dataType': 'json',
                success: function(result) {
                    $('#forwarder_info_2').empty();

                    let forwarder_data = "<option value='0' >" + 'Select Forwarder' + "</option>";
                    $.each(result, function(i, result) {
                        forwarder_data += "<option value='" + result.id + "'>" + result
                            .user_name +
                            "</option>";
                    });
                    $('#forwarder_info_2').append(forwarder_data);
                },
                error: function(result) {
                    alert('Error.. Please Contact Admin');
                    return false;
                }

            });
        }
    };

    function append_data(response) {

        console.log(response);
        $('.table').removeClass('d-none')
        $('.create_shipmtn_btn').removeClass('d-none')
        let html = '';
        html += "<tr class='table_row'>";
        html += "<td name='consignor[]'>" + response.data.consignor + "</td>";
        html += "<td name='consignee[]'>" + response.data.consignee + "</td>";
        html += "<td name='awb_number[]'>" + response.data.awb_number + "</td>";
        html += "<td name='order_id[]'>" + response.data.order_id + "</td>";
        html += "<td name='packet_details[]'>" + response.data.packet + "</td>";
        html += "<td name='purchase_tracking_id[]'>" + response.data.purchase_tracking_id + "</td>";
        html += '<td> <button type="button" id="remove" class="ml-2 btn btn-sm btn-danger remove1">Remove</button></td>'
        html += "</tr>";

        return html;
    }

    // create Shipment//
    $(".create_shipmtn_btn").on("click", function() {
        $(this).prop('disabled', true);
        let forwarder_info_2 = $('#forwarder_info_2').val();
        let forwarder_2_awb = $('#forwarder_2_awb').val();
        let mode = $('#mode').val();

        let validation = true;
        if (mode == 0) {
            alert('Mode Required.. please select Mode....');
            validation = false;
            return false;
        }
        if (forwarder_info_2 == 0) {
            alert('Forwarder 2 Required.. please select Forwarder 2....');
            $('.create_shipmtn_btn').prop('disabled', false);
            validation = false;
            return false;
        }
        if (forwarder_2_awb == '') {
            alert('Forwarder 2 AWB Required.. please Enter AWB...');
            $('.create_shipmtn_btn').prop('disabled', false);
            validation = false;
            return false;
        }
        if (validation) {
            let self = $(this);
            let table = $("#report_table tbody tr");
            let data = new FormData();
            table.each(function(index, elm) {
                let td = $(this).find('td');

                data.append('awb[]', td[2].innerText);
                data.append('Order_id[]', td[3].innerText);
                data.append('purchase_tracking_id[]', td[5].innerText);
            });

            let mode = $('#mode').val();
            data.append('mode', mode);
            data.append('forwarder_2', forwarder_info_2);
            data.append('forwarder_2_awb', forwarder_2_awb);
            $.ajax({
                method: 'POST',
                url: "{{route('shipntrack.outward.store')}}",
                data: data,
                processData: false,
                contentType: false,
                response: 'json',
                success: function(response) {
                    $('.create_shipmtn_btn').prop('disabled', false);
                    if (response.success) {
                        // getBack();
                        alert('Outward Shipment has created successfully');
                        location.reload();
                    }
                },
                error: function(response) {
                    // console.log(response);
                    alert('Something Went Wrong.. Try creating shipment Again');
                    $('.create_shipmtn_btn').prop('disabled', false);
                }
            });
        }


    });

    // // *Redirect to Index:*//
    // function getBack() {
    //     Location.reload();
    //     window.location.href = '/shipntrack/outward?success=Shipment has been created successfully'

    // }

    /*Delete Row :*/
    $('#report_table').on('click', ".remove1", function() {
        $(this).closest("tr").remove();
    });
</script>
@stop