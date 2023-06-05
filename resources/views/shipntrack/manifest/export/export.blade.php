@extends('adminlte::page')

@section('title', 'SNT Export')

@section('css')

<link rel="stylesheet" href="/css/styles.css">
<style>
    #collapsesix .form-group {
        width: 49%;
        margin-right: 5px;
    }

    .form-group {
        margin-bottom: 0;

    }
</style>

@stop
@section('content_header')
<div class="row align-items-center">
    <div class="col-0.5 pr-4">
        <a href="{{route('shipntrack.export')}}">
            <x-adminlte-button label="Back" class="btn-sm" theme="primary" icon="fas fa-arrow-left" />
        </a>
    </div>
    <div id="type" class="row col-3">
        <div class="form-check col-6">
            <input class="form-check-input" type="radio" name="flexRadioDefault" id="single">
            <label class="form-check-label" for="single">
                Export Single AWB
            </label>
        </div>
        <div class="form-check col-6">
            <input class="form-check-input" type="radio" name="flexRadioDefault" id="bulk">
            <label class="form-check-label" for="bulk">
                Export Bulk
            </label>
        </div>
    </div>

    <div class="col">
        <h1 class="m-0 text-dark">Shipntrack Export Manifest </h1>
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
    </div>
</div>
<div class="row align-items-center">
    <div class="col-2">
        <div class="form-group dest d-none">
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

    <div class="col-2 form-group awb type d-none">
        <x-adminlte-input label='Enter AWB :' type='text' name='awb' id="awb" placeholder='Enter AWB here..' required />
    </div>

    <div class="col-2 form-group bulk type d-none">
        <x-adminlte-input label='Enter Manifest ID :' type='text' name='bulk' id="bulkdata" placeholder='Enter Manifest ID here..' required />
    </div>





    <div id="collapsesix" class="show col-4 d-none">
        <div class="py-1 row">
            <x-adminlte-select label="Forwarder 1:" name="forwarder1" id="forwarder_info_1" value="{{ old('forwarder2') }}" required>
                <option value='0'> Forwarder 1</option>
            </x-adminlte-select>
            <x-adminlte-input label="Forwarder 1 AWB :" name="forwarder_1_awb" type="text" placeholder="Forwarder 1 AWB" id="forwarder_1_awb" value="{{ old('forwarder_1_awb') }}" required />
        </div>
    </div>


    <div class="col text-right">
        <div style="margin-top: 1.8rem;">
            <x-adminlte-button label="Create Export Manifest" theme="primary" icon="fas fa-plus" id="create" class="btn-sm d-none create_shipmtn_btn" />
        </div>
    </div>
</div>

<br>
<table class="table table-bordered yajra-datatable table-striped d-none" id="report_table">
    <thead>
        <tr class="table-info table  ">
            <th>AWB</th>
            <th>In-Scan Date</th>
            <th>Destination</th>
            <th>manifest Id</th>
            <th>Order ID</th>
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

        if ($("#single").is(":checked")) {

            $('.bulk').addClass('d-none')
            $('.awb').removeClass('d-none');
        } else if ($("#bulk").is(":checked")) {

            $('.awb').addClass('d-none');
            $('.bulk').removeClass('d-none')
        }

    });

    $("#type").on('change', function(e) {
        $('.dest').removeClass('d-none');
    });

    // if ($("#single").is(":checked")) {
    $('input[name="single"]:checked');
    {
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
                    url: "{{route('shipntrack.export.single.fetch')}}",
                    data: {
                        'awb': data,
                        'mode': mode,
                        'type': 'single',
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(result) {
                        if (result.hasOwnProperty("error")) {
                            alert('Invalid Manifest ID or No data Found..');
                            return false;
                        }
                        let table = $("#table_body");
                        table.append(append_data(result))
                        $('#awb').val('');
                        $('.show').removeClass('d-none');
                        forwarder();
                    },
                    error: function() {
                        alert('Invalid AWB NO..');
                    }
                });
            }
        });

    }

    //bulk export
    $('input[name="bulk"]:checked');
    {
        $(document).on("focusout", "#bulkdata", function(e) {
            e.stopPropagation();

            let mode = $('#mode').val();

            let validation = true;
            if (mode == 0) {
                alert('Mode Rquired.. please select Mode....');
                validation = false;
                return false;
            }
            let data = $('#bulkdata').val();

            if (validation) {
                $.ajax({
                    method: 'get',
                    url: "{{route('shipntrack.export.single.fetch')}}",
                    data: {
                        'awb': data,
                        'mode': mode,
                        'type': 'bulk',
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(result) {
                        if (result.hasOwnProperty("error")) {
                            alert('Invalid Manifest ID or No data Found..');
                            return false;
                        }
                        let table = $("#table_body");
                        table.append(append_data(result))
                        $('#bulkdata').val('');
                        $('.show').removeClass('d-none');
                        forwarder();
                    },
                    error: function(result) {
                        alert('Error.. Please Contact Admin');
                        return false;
                    }
                });
            }
        });

    }

    //Append data
    function append_data(response) {

        console.log((response))
        $('.table').removeClass('d-none')
        $('.create_shipmtn_btn').removeClass('d-none')

        let html = '';

        $.each(response.data, function(index, value) {
            const d = new Date(value.created_at);

            console.log(value);

            html += "<tr class='table_row'>";
            html += "<td name='awb_number[]'>" + value.awb_number + "</td>";
            html += "<td name='booking_date[]'>" + d.toDateString() + "</td>";
            html += "<td name='consignor[]'>" + value.process.destination + "</td>";
            html += "<td name='manifest_id[]'>" + value.manifest_id + "</td>";
            html += "<td name='order_id[]'>" + value.order_id + "</td>";
            html += "<td name='purchase_tracking_id[]'>" + value.purchase_tracking_id + "</td>";
            html += '<td> <button type="button" id="remove" class="btn btn-sm btn-danger remove1">Remove</button></td>'
            html += "</tr>";

        });


        return html;
    }

    //Forwarder APpend
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
                    console.log(result)
                    $('#forwarder_info_1').empty();

                    let forwarder_data = "<option value='0' >" + 'Select Forwarder' + "</option>";
                    $.each(result, function(i, result) {
                        forwarder_data += "<option value='" + result.id + "'>" + result
                            .user_name +
                            "</option>";
                    });
                    $('#forwarder_info_1').append(forwarder_data);
                },
                error: function(result) {
                    alert('Error.. Please Contact Admin');
                    return false;
                }

            });
        }
    };

    // create Shipment//
    $(".create_shipmtn_btn").on("click", function() {
        $(this).prop('disabled', true);
        let forwarder_info_1 = $('#forwarder_info_1').val();
        let forwarder_1_awb = $('#forwarder_1_awb').val();

        let mode = $('#mode').val();
        let validation = true;
        if (mode == 0) {
            alert('Mode Required.. please select Mode....');
            $('.create_shipmtn_btn').prop('disabled', false);
            validation = false;
            return false;
        }
        if (forwarder_info_1 == 0) {
            alert('Forwarder 1 Required.. please select Forwarder 1....');
            $('.create_shipmtn_btn').prop('disabled', false);
            validation = false;
            return false;
        }
        if (forwarder_1_awb == '') {
            alert('Forwarder 1 AWB Required.. please Enter AWB...');
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

                data.append('awb[]', td[0].innerText);
                data.append('booking_date[]', td[1].innerText);
                data.append('destination[]', td[2].innerText);
                data.append('inscan_manefist[]', td[3].innerText);
                data.append('order_id[]', td[4].innerText);
                data.append('tracking[]', td[5].innerText);

            });
            let mode = $('#mode').val();
            data.append('mode', mode);
            data.append('forwarder_1', forwarder_info_1);
            data.append('forwarder_1_awb', forwarder_1_awb);


            $.ajax({
                method: 'POST',
                url: "{{route('shipntrack.export.store')}}",
                data: data,
                processData: false,
                contentType: false,
                response: 'json',
                success: function(response) {
                    $('.create_shipmtn_btn').prop('disabled', false);
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


    });


    // *Redirect to Index:*//
    function getBack() {
        window.location.href = '/shipntrack/export/manifest?success=Export has been created successfully'
    }

    /*Delete Row :*/
    $('#report_table').on('click', ".remove1", function() {
        $(this).closest("tr").remove();
    });
</script>
@stop