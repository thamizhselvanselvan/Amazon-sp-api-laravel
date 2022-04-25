@extends('adminlte::page')

@section('title', 'BOE Export')
@section('css')

<link rel="stylesheet" href="/css/styles.css">

<!-- <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" /> -->

@stop


@section('content_header')

<h1 class="m-0 text-dark">BOE Export Filter</h1>
@stop

@section('content')
<!-- <div class="row">
    <div class="col-6sm">
        <a href="/BOE/index" class="btn btn-primary btn-xs">
            <i class="fas fa-long-arrow-alt-left"></i> Back
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

<form action="{{ route('BOE.Export.Filter') }}" method="POST" id="boe_export_filter" class="row">
    @csrf
    <div class="col">
        <x-adminlte-select label="Company" name="company" id="company">
            @if($role == 'Admin')
            <option value="0">ALL</option>
            @endif
            @foreach ($companys as $company)
            <option value="{{ $company->id }}"> {{ $company->company_name }}</option>
            @endforeach
        </x-adminlte-select>
    </div>
    <!-- <div class="col-3">
        <x-adminlte-input label="Name Of Consignor" name="email" id="email" type="text" placeholder="" value="{{ old('email') }}" />
    </div> -->
    <div class="col">
        <div class="form-group">
            <label>Date Of Arrival:</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="far fa-calendar-alt"></i>
                    </span>
                </div>
                <input type="text" class="form-control float-right datepicker" name='date_of_arrival' autocomplete="off" id="date_of_arrival">
            </div>

        </div>
        <!-- <x-adminlte-input label="Date Of Arrival" name="email" id="email" type="text" placeholder="Email" value="{{ old('email') }}" /> -->
    </div>
    <div class="col">
        <div class="form-group">
            <label>Challan Date:</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="far fa-calendar-alt"></i>
                    </span>
                </div>
                <input type="text" class="form-control float-right datepicker" name='challan_date' autocomplete="off" id="challan_date">
            </div>

        </div>
    </div>
    <div class="col">
        <div class="form-group">
            <label>Upload Date:</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="far fa-calendar-alt"></i>
                    </span>
                </div>
                <input type="text" class="form-control float-right datepicker" name='upload_date' autocomplete="off" id="upload_date">
            </div>

        </div>
    </div>
    <div class="col-2">
        <div style="margin-top: 2.4rem;">
            <x-adminlte-button label="Search" theme="primary" icon="fas fa-search" id="search" class="btn-sm" />
            <x-adminlte-button label="Export" theme="primary" icon="fas fa-file-export" id='export_boe' class="btn-sm" />
        </div>
    </div>
</form>
<div class="row">

</div>
<br>
<table class="table table-bordered yajra-datatable table-striped" id="report_table">
    <thead>
        <tr>
            <td>S/N</td>
            <!-- <td>Company Name</td> -->
            <td>AWB No.</td>
            <td>Courier Registration Number</td>
            <td>Name of the Authorized Courier</td>
            <td>Name of Consignor</td>
            <td>Name of Consignee</td>
            <td>BOE Booking Rate</td>
            <td>Duty</td>
            <td>SW Srchrg</td>
            <td>Insurance</td>
            <td>IGST</td>
            <td>Total(Duty+Cess+IGST)</td>
            <td>Interest</td>
            <td>CBX II No</td>
            <td>HSN Code</td>
            <td>Qty</td>
            <td>Date of Arrival</td>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>

@stop

@section('js')

<script type="text/javascript">
    $(document).ready(function($) {
        $("#report_table").hide();
        $('.datepicker').daterangepicker({
            autoUpdateInput: false,
            locale: {
                format: 'YYYY-MM-DD',
            },
        });

        $('.datepicker').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
        });

        $('.datepicker').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });

    });
    $('#export_boe').on('click', function(e) {
        e.preventDefault();
        $('#boe_export_filter').submit();
    });

    $("#search").on('click', function(e) {
        $("#report_table").show();

        let company = $('#company').val();
        let date_of_arrival = $("#date_of_arrival").val();
        let challan_date = $('#challan_date').val();
        let upload_date = $("#upload_date").val();

        let yajra_table = $('.yajra-datatable').DataTable({

            destroy: true,
            processing: true,
            serverSide: true,
            ajax: ("{{ url('BOE/Export/view') }}", {

                data: {
                    company: company,
                    date_of_arrival: date_of_arrival,
                    upload_date: upload_date,
                    challan_date: challan_date
                },
            }),
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'hawb_number',
                    name: 'hawb_number'
                },
                {
                    data: 'courier_registration_number',
                    name: 'courier_registration_number',
                    orderable: false,
                },
                {
                    data: 'name_of_the_authorized_courier',
                    name: 'name_of_the_authorized_courier',
                    orderable: false,
                },
                {
                    data: 'name_of_consignor',
                    name: 'name_of_consignor',
                },
                {
                    data: 'name_of_consignee',
                    name: 'name_of_consignee'
                },
                {
                    data: 'rateof_exchange',
                    name: 'rateof_exchange',
                    orderable: false,
                    searchable: false
                },

                {
                    data: 'duty',
                    name: 'duty',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'swsrchrg',
                    name: 'swsrchrg',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'insurance',
                    name: 'insurance',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'igst',
                    name: 'igst',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'duty_rs',
                    name: 'duty_rs',
                    
                },
                {
                    data: 'interest',
                    name: 'interest',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'cbe_number',
                    name: 'cbe_number',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'ctsh',
                    name: 'ctsh',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'quantity',
                    name: 'quantity'
                },
                {
                    data: 'date_of_arrival',
                    name: 'date_of_arrival'
                },

            ]
        });
        // alert("search");
    });
</script>
@stop