@extends('adminlte::page')

@section('title', 'SNT In-Scan')

@section('css')

<link rel="stylesheet" href="/css/styles.css">
@stop
@section('content_header')
<div class="row">
    <div class="col-0.5">
            <a href="{{route('shipntrack.inscan.view')}}">
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
                <option value={{ $destination['source'] . '-' . $destination['destination'] }}>
                    {{ $destination['source'] . '-' . $destination['destination'] }}
                </option>
                @endforeach
            </x-adminlte-select>
        </div>
    </div>
    <div class="col-2">
        <div class="form-group awb type d-none">
            <x-adminlte-input label='Enter AWB :' type='text' name='awb' id="awb" placeholder='Enter awb here' onkeyup="keyupFunction()" required />
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

<br>
<table class="table table-bordered yajra-datatable table-striped" id="report_table">
    <thead>
        <tr class="table-info table d-none ">
            <th>AWB</th>
            <th>Mode</th>
            <th>Type</th>
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
        $('.awb').removeClass('d-none');
    });

    function keyupFunction() {
        let mode = $('#mode').val();
        let validation = true;
        if (mode == 0) {
            alert('Mode Required.. please select Mode....');
            validation = false;
            return false;
        }
        let data = $('#awb').val();
        alert(data);

        $.ajax({
            method: 'get',
            url: "{{route('shipntrack.inscan.get')}}",
            data: {
                'awb': data,
                'mode': mode,
                "_token": "{{ csrf_token() }}",
            },
            success: function(result) {
                alert('success');
            },
            error: function() {
                alert('ERROR');
            }
        });
    }






    $(".upload_awb_btn").on("click", function() {
        let uploaded = $('.up_awb').val();
        let mode = $('#mode').val();
        let type = $('#type').val();
        let validation = true;
        if (mode == 0) {
            alert('Mode Required.. please select Mode....');
            validation = false;
            return false;
        }
        if (uploaded == '') {
            alert('AWB Required.. please enter AWB....');
            validation = false;
            return false;
        }
        if (type == 0) {
            alert('Type Required.. please enter Type....');
            validation = false;
            return false;
        }
        if (validation) {
            let table = $("#table_body");
            table.append(append_data(uploaded, mode, type))
        }
    });

    function append_data(response, mode, type) {
        $('.table').removeClass('d-none')
        $('.create_shipmtn_btn').removeClass('d-none')
        let html = '';
        var strarray = response.split(',');
        for (var i = 0; i < strarray.length; i++) {
            html += "<tr class='table_row'>";
            html += "<td name='awb_number[]'>" + strarray[i] + "</td>";
            html += "<td name='mode[]'>" + mode + "</td>";
            html += "<td name='type[]'>" + type + "</td>";
            html += '<td><input type="text-area" class="w-75" value="Receved At Source Warehouse.." name="status[]" id="status">  </td>'
            html += '<td> <button type="button" id="remove" class="btn btn-sm btn-danger remove1">Remove</button></td>'
            html += "</tr>";
        }

        return html;
    }


    // create Shipment//
    $(".create_shipmtn_btn").on("click", function() {
        $(this).prop('disabled', true);

        let mode = $('#mode').val();
        let type = $('#type').val();
        let validation = true;
        if (mode == 0) {
            alert('Mode Required.. please select Mode....');
            validation = false;
            return false;
        }
        if (type == 0) {
            alert('Type Required.. please enter Type....');
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
                data.append('mode[]', td[1].innerText);
                data.append('type[]', td[2].innerText);
                data.append('status[]', td[3].children[0].value);

            });


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