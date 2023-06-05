@extends('adminlte::page')

@section('title', 'SNT In-Scan')

@section('css')

<link rel="stylesheet" href="/css/styles.css">
@stop
@section('content_header')
<div class="row">
    <div class="col-0.5">
        <a href="{{route('shipntrack.inscan')}}">
            <x-adminlte-button label="Back" class="btn-sm" theme="primary" icon="fas fa-arrow-left" />
        </a>
    </div>
    <div class="col text-center">
        <h1 class="m-0 text-dark">Shipntrack IN-Scan </h1>
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
<div class="row">
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
            <x-adminlte-input label='Enter Tracking ID :' type='text' name='awb' id="awb" placeholder='Enter Tracking ID here..' required />
        </div>
    </div>

    <div class="col text-right">
        <div style="margin-top: 1.8rem;">
            <x-adminlte-button label="Create Manifest" theme="primary" icon="fas fa-plus" id="create" class="btn-sm d-none create_shipmtn_btn" />
        </div>
    </div>
</div>

<br>
<table class="table table-bordered yajra-datatable table-striped d-none" id="report_table">
    <thead>
        <tr class="table-info table  ">
            <th>AWB</th>
            <th>Booking Date</th>
            <th>Consignor</th>
            <th>Consignee</th>
            <th>Order ID</th>
            <th>Tracking ID</th>
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
        console.log(data)


        if (validation) {
            $.ajax({
                method: 'get',
                url: "{{route('shipntrack.inscan.get')}}",
                data: {
                    'awb': data,
                    'mode': mode,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(result) {
                    let table = $("#table_body");
                    table.append(append_data(result))
                    $('#awb').val('');
                },
                error: function() {
                    alert('Invalid ID or No Data Found..');
                }
            });
        }
    });

    function append_data(response) {

        console.log((response))
        $('.table').removeClass('d-none')
        $('.create_shipmtn_btn').removeClass('d-none')
        let html = '';

        html += "<tr class='table_row'>";
        html += "<td name='awb_number[]'>" + response.awb + "</td>";
        html += "<td name='booking_date[]'>" + response.booking_date + "</td>";
        html += "<td name='consignor[]'>" + response.consignor + "</td>";
        html += "<td name='consignee[]'>" + response.consignee + "</td>";
        html += "<td name='order_id[]'>" + response.order_id + "</td>";
        html += "<td name='purchase_tracking_id[]'>" + response.purchase_tracking_id + "</td>";
        html += '<td> <button type="button" id="remove" class="btn btn-sm btn-danger remove1">Remove</button></td>'
        html += "</tr>";


        return html;
    }


    // create Shipment//
    $(".create_shipmtn_btn").on("click", function() {
        $(this).prop('disabled', true);

        let mode = $('#mode').val();
        let validation = true;
        if (mode == 0) {
            alert('Mode Required.. please select Mode....');
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
                data.append('consignor[]', td[2].innerText);
                data.append('consignee[]', td[3].innerText);
                data.append('Order_id[]', td[4].innerText);
                data.append('tracking[]', td[5].innerText);


            });
            let mode = $('#mode').val();
            data.append('mode', mode);
            $.ajax({
                method: 'POST',
                url: "{{route('shipntrack.inscan.store')}}",
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
        window.location.href = '/shipntrack/in-scan?success=Shipment has been created successfully'
    }

    /*Delete Row :*/
    $('#report_table').on('click', ".remove1", function() {
        $(this).closest("tr").remove();
    });
</script>
@stop