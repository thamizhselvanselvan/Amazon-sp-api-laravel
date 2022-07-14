@extends('adminlte::page')
@section('title', 'Search Invoice')

@section('content_header')
<div class="row">
    <h1 class="m-0 text-dark col">Invoice Management</h1>
    <h6 class="mb-4 text-right col">
        <div>
            <label class="">
                Search:
            </label>
            <input type="text" id="Searchbox" class="d-inline-block" placeholder="search invoice" autocomplete="off" />
        </div>
    </h6>
</div>
<!-- <div class="row">
    <div class="col">
        <a href="{{ route('invoice.index') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-long-arrow-alt-left"></i> Back
        </a>
    </div>
</div> -->
@stop
@section('content')
<div class="row">
    <div class="col">

        <div class="alert_display">
            @if ($message = Session::get('success'))
            <div class="alert alert-warning alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $message }}</strong>
            </div>
            @endif
        </div>
    </div>
</div>
<div class="container-fluid search-box">
    <div class="row">
        <div class="col pt-2">
            <div class="mt-4">
                <a href="upload">
                    <x-adminlte-button label="Add Records" theme="primary" icon="fas fa-file-upload"
                        class="btn-md ml-2" />
                </a>
                <a href="template/download">
                    <x-adminlte-button label="Download Template" theme="primary" icon="fas fa-download"
                        class="btn-md ml-1" />
                </a>
                <a href="zip/download">
                    <x-adminlte-button label="Download Invoice Zip" theme="primary" icon="fas fa-download"
                        class="btn-md ml-1" id='zip-download' />
                </a>
            </div>
        </div>


        <div class="col-7 d-flex justify-content-end">
            <div class="form-group mr-2">
                <x-adminlte-select label="Mode: " name="mode" id="mode" class="float-right">
                    <option value='NULL'>Select Mode</option>
                    @foreach ($mode as $value)
                    <option value="{{$value->mode}} ">{{$value->mode}}</option>
                    @endforeach
                </x-adminlte-select>
                <p class="vmode" id="vmode"></p>
            </div>
            <div class="form-group bag_no mr-2">
                <x-adminlte-input label="Bag No.:" name="bag_no" id="bag_no" placeholder="Bag No.">

                </x-adminlte-input>
            </div>
            <div class="form-group">
                <label>Invoice Date:</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            <i class="far fa-calendar-alt"></i>
                        </span>
                    </div>
                    <input type="text" class="form-control float-right datepicker" name='invoice_date'
                        placeholder="Select Date Range" autocomplete="off" id="invoice_date">
                    <x-adminlte-button label="Search" theme="primary" icon="fas fa-search" id="search"
                        class="btn-sm ml-2" />
                    <x-adminlte-button label="Download Selected" id="selected-download" theme="primary"
                        icon="fas fa-download" class="btn-sm ml-2" />
                    <x-adminlte-button label="Print Selected" id='select_print' theme="primary" icon="fas fa-print"
                        class="btn-sm ml-2" />
                </div>
            </div>
        </div>
    </div>
</div>
<div id="showTable" class="d-none">
    <table class='table table-bordered table-striped text-center'>
        <thead>
            <tr class='text-bold bg-info'>
                <th>Select All <input type='checkbox' id='selectAll'></th>
                <th>Invoice No.</th>
                <th>Invoice Date</th>
                <th>Mode</th>
                <th>Channel</th>
                <th>Shipped By</th>
                <th>AWB No.</th>
                <th>Store Name</th>
                <th>Bill To Name</th>
                <th>Ship To Name</th>
                <th>SKU</th>
                <th>QTY</th>
                <th>Price</th>
                <th class='text-center'>Action</th>
            </tr>
        </thead>
        <tbody id='checkTable'>
        </tbody>
    </table>
</div>
@stop

@section('js')
<script type="text/javascript">
$(document).ready(function() {
    //start search invoice
    // $('#zip-download').hide();
    $("#Searchbox").on('keyup', function() {
        let self = $(this);
        let invoice_no = $.trim(self.val());
        let invoice_no_re = invoice_no.replaceAll(/-/g, '_');
        let tr = $("." + invoice_no_re);
        let table = $("#checkTable");

        $(tr.children().children()[0]).prop('checked', true);
        $(tr).addClass('bg-warning');
        tr.prependTo(table);
    });
    //end search invoice

    // $('#showTable').css("display", "none");
    $(".datepicker").daterangepicker({
        autoUpdateInput: false,
        locale: {
            format: 'YYYY-MM-DD',
        },
    });
    $('.datepicker').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format(
            'YYYY-MM-DD'));
    });

    $('.datepicker').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });
    $('#mode').on('change', function() {
        if ($('#mode').val() != 'NULL') {
            var id = document.getElementById('mode');
            id.style = ' none';
            document.getElementById('vmode').innerHTML = '';
        }
    });
    $('#search').click(function() {
        if ($('#mode').val() == 'NULL') {
            var id = document.getElementById('mode');
            id.style = 'border: 2px solid red';
            let text = 'Mode must be filled out';
            document.getElementById('vmode').innerHTML = text;
            document.getElementById('vmode').style.color = 'red';
        } else {

            $('#showTable').removeClass("d-none");
            let bag_no = $('#bag_no').val();
            let invoice_mode = $('#mode').val();
            let invoice_date = $('#invoice_date').val();
            $.ajax({
                method: 'POST',
                url: "{{ url('/invoice/select-invoice')}}",
                data: {
                    "bag_no": bag_no,
                    "invoice_date": invoice_date,
                    "invoice_mode": invoice_mode,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(response) {
                    console.log(response);
                    let table_data = '';

                    $.each(response, function(i, response) {
                        let invoice_id = response.invoice_no.replaceAll(
                            /-/g,
                            '_');

                        table_data += "<tr class='" + invoice_id +
                            "'><td><input class='check_options' type='checkbox' value=" +
                            response.id + " name='options[]' id='checkid" +
                            response
                            .id + "'></td><td>" + response.invoice_no +
                            "</td><td>" + response.invoice_date +
                            "</td><td>" +
                            response.mode + "</td><td>" + response.channel +
                            "</td><td>" + response.shipped_by +
                            "</td><td>" +
                            response.awb_no + "</td><td>" + response
                            .store_name +
                            "</td><td>" + response.bill_to_name +
                            "</td><td>" +
                            response.ship_to_name + "</td><td>" + response
                            .sku +
                            "</td><td>" + response.qty + "</td><td>" +
                            response
                            .currency + ' ' + response.product_price +
                            "</td><td><div class='d-flex'><a href=/invoice/convert-pdf/" +
                            response.invoice_no +
                            " class='edit btn btn-success btn-sm' target='_blank'><i class='fas fa-eye'></i> View </a><div class='d-flex pl-2'><a href=/invoice/download-direct/" +
                            response.invoice_no +
                            " class='edit btn btn-info btn-sm'><i class='fas fa-download'></i> Download </a>";
                        table_data +=
                            "<div class='d-flex pl-2'><a href=/invoice/edit/" +
                            response.invoice_no +
                            " class='edit btn btn-primary btn-sm'><i class='fas fa-edit'></i> Edit </a></td> </tr>"
                    });
                    $('#checkTable').html(table_data);
                },
            });
        }

    });

    $('#selected-download').click(function() {
        alert('Invoice is downloading please wait.');
        let invoice_mode = $('#mode').val();
        let invoice_date = $('#invoice_date').val();
        var url = $(location).attr('href');
        let id = '';
        let count = 0;
        let arr = '';
        $("input[name='options[]']:checked").each(function() {
            if (count == 0) {
                id += $(this).val();
            } else {
                id += '-' + $(this).val();
            }
            count++;
        });
        $.ajax({
            method: 'POST',
            url: "{{ url('/invoice/select-download')}}",
            data: {
                'id': id,
                "invoice_date": invoice_date,
                "invoice_mode": invoice_mode,
                "_token": "{{ csrf_token() }}",
            },
            success: function(response) {
                // arr += response;
                // window.location.href = '/invoice/zip-download/' + arr;
                // alert('Export pdf successfully');
            },
        });
    });

    $('#select_print').click(function() {
        var url = $(location).attr('href');
        let id = '';
        let count = 0;
        let arr = '';
        $("input[name='options[]']:checked").each(function() {
            if (count == 0) {
                id += $(this).val();
            } else {
                id += '-' + $(this).val();
            }
            count++;
            window.location.href = '/invoice/selected-print/' + id;
        });
    });

});
$('#selectAll').change(function() {
    if ($('#selectAll').is(':checked')) {
        $('.check_options').prop('checked', true);
    } else {
        $('.check_options').prop('checked', false);
    }
});
$("input[name='options[]']").on('change', function() {

    let input_checkbox = $("input[name='options[]'] ").length;
    let total_input_checkbox = $("input[name='options[]']:checked").length;
    alert(input_checkbox);
    alert(total_input_checkbox);
    if (input_checkbox === total_input_checkbox) {
        $('#selectAll').prop('checked', true);
    } else {
        $('#selectAll').prop('checked', false);
    }
});
</script>
@stop
