@extends('adminlte::page')

@section('title', 'SNT Inward')

@section('css')
<style>
    .table td {
        padding-left: 10px !important;
    }
</style>
<link rel="stylesheet" href="/css/styles.css">
@stop
@section('content_header')
<div class="row">
    <div class="col-0.5">
        <a href="{{route('shipntrack.inward')}}">
            <x-adminlte-button label="Back" class="btn-sm" theme="primary" icon="fas fa-arrow-left" />
        </a>
    </div>
    <div class="col text-center">
        <h1 class="m-0 text-dark">SNT Inward Shipment </h1>
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
            <x-adminlte-input label='Enter International AWB :' type='text' name='awb' id="awb" placeholder='Enter Forwarder AWB here..' required />
        </div>
    </div>
    <div class="col-2 local d-none">
        <div class=" awb type">
            <x-adminlte-input label='Enter Local AWB :' type='text' name='local' id="local" placeholder='Enter Local AWB here..' required />
        </div>
    </div>

    <div class="col text-right">
        <div style="margin-top: 1.8rem;">
            <x-adminlte-button label="Generate new Shipment" theme="primary" icon="fas fa-plus" id="create" class="btn-sm d-none create_shipmtn_btn" />
        </div>
    </div>
</div>

<br>
<table class="table table-bordered yajra-datatable table-striped d-none" id="report_table">
    <thead>
        <tr class="table-info table  ">
            <th>Total Items</th>
            <th>AWB</th>
            <th>International AWB Number</th>
            <th>Destination</th>
            <th>purchase Tracking ID</th>
            <th>Order ID</th>
            <th>Received Status</th>
            <!-- <th>Action</th> -->
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
                url: "{{route('shipntrack.inward.get')}}",
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
                    $('.local').removeClass('d-none');
                    // $('#awb').val('');
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
        $.each(response.data, function(index, value) {
            let item_class = 'item_' + value.awb;
            html += "<tr class='table_row'>";
            // html += "<td name='total_items[]'>" + value.total_items + "</td>";
            html += "<td name='total_items[]'>" + value.total_items + "</td>";
            html += "<td name='awb_number[]'>" + value.awb + "</td>";
            html += "<td name='international_awb_number[]'>" + value.international_awb_number + "</td>";
            html += "<td name='destination[]'>" + value.destination + "</td>";
            html += "<td name='purchase_tracking_id[]'>" + value.purchase_tracking_id + "</td>";
            html += "<td name='order_id[]'>" + value.order_id + "</td>";
            // html += '<td> <input type="checkbox" value="1" name="checkbox[]" id="checkbox"> </td>';
            html += "<td name='status[]' class='" + item_class + " status'>" + '-NO-' + "</td>";
            // html += '<td> <button type="button" id="remove" class="ml-2 btn btn-sm btn-danger remove1">Remove</button></td>'
            html += "</tr>";

        });
        return html;
    }


    // local scan
    $(document).on("focusout", "#local", function(e) {
        e.stopPropagation();

        let mode = $('#mode').val();
        let validation = true;
        if (mode == 0) {
            alert('Mode Required.. please select Mode....');
            validation = false;
            return false;
        }
        let data = $('#local').val();
        console.log(data);

        if (validation) {
            $.ajax({
                method: 'get',
                url: "{{route('shipntrack.inward.verify')}}",
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
                    ref_existing_data(result);
                },
                error: function() {
                    alert('Invalid ID or No Data Found..');
                }
            });
        }
    });


    function ref_existing_data(response) {

        let html = '';
        let awb = response.data[0].awb;
        if (awb) {
            let id = '.item_' + awb;
            let title = '-YES-';
            $(id).text(title);
        } else {
            alert('Something Went Wrong. Contact Admin.');
        }

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

                data.append('total_item', td[0].innerText);
                data.append('awb[]', td[1].innerText);
                data.append('international_awb_number', td[2].innerText);
                data.append('purchase_tracking_id[]', td[4].innerText);
                data.append('Order_id[]', td[5].innerText);
                data.append('status[]', td[6].innerText);
                // var $chkbox = $(this).find('input[type="checkbox"]');
                // if ($chkbox.length) {
                //     var status = $chkbox.prop('checked');
                //     data.append('chkbox[]', status);
                // }

            });

            let mode = $('#mode').val();
            data.append('mode', mode);

            $.ajax({
                method: 'POST',
                url: "{{route('shipntrack.inward.store')}}",
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
        window.location.href = '/shipntrack/inward?success=Shipment has been created successfully'
    }

    /*Delete Row :*/
    $('#report_table').on('click', ".remove1", function() {
        $(this).closest("tr").remove();
    });
</script>
@stop