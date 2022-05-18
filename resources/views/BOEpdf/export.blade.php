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
            <x-adminlte-button label="Export" theme="primary" icon="fas fa-file-export" id='export_boe' class="btn-sm exportboe_modal_open" />
        </div>
    </div>
</form>

<!-- Start modal -->

<!-- Modal -->
<div class="modal fade" id="exportboe" tabindex="-1" role="dialog" aria-labelledby="exportboeModalLabel" aria-hidden="true">

    <div class="modal-dialog modal-xl" role="document">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title" id="exportboeModalLabel">Select Headers</h5>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">

                    <span aria-hidden="true">&times;</span>

                </button>
            </div>

            <div class="modal-body" id="checkbox">

                <div class="form-check d-flex row">

                    <div class="col-12">
                        <input type="checkbox" class="form-check-input all" value="all" id="all">
                        <h6>Select All</h6>
                    </div>

                </div>

                <hr>

                <div class=" form-check d-flex row">

                    <div class="col-3">
                        <input type="checkbox" class="form-check-input header_option" name='boedata[]' value="current_status_of_the_cbe" id="current_status_of_the_cbe">
                        <h6>Awb No </h6>
                    </div>

                    <div class="col-3">
                        <input type="checkbox" class="form-check-input header_option" name='boedata[]' value="courier_registration_number" id="courier_registration_number">
                        <h6>Reg No</h6>
                    </div>

                    <div class="col-3">
                        <input type="checkbox" class="form-check-input header_option" name='boedata[]' value="cbe_number" id="cbe_number">
                        <h6>Cbe Number </h6>
                    </div>

                    <div class="col-3">
                        <input type="checkbox" class="form-check-input header_option" name='boedata[]' value="name_of_the_authorized_courier" id="name_of_the_authorized_courier">
                        <h6>Authorized Courier </h6>
                    </div>

                </div>

                <hr>

                <div class="form-check d-flex row">


                    <div class="col-3">
                        <input type="checkbox" class="form-check-input header_option" name='boedata[]' value="address_of_authorized_courier" id="address_of_authorized_courier">
                        <h6>Authorized Courier Add. </h6>
                    </div>

                    <div class="col-3">
                        <input type="checkbox" class="form-check-input header_option" name='boedata[]' value="airport_of_shipment" id="airport_of_shipment">
                        <h6>Airport Of Shipment </h6>
                    </div>

                    <div class="col-3">
                        <input type="checkbox" class="form-check-input header_option" name='boedata[]' value="country_of_exportation" id="country_of_exportation">
                        <h6>Country Of Exportation </h6>
                    </div>

                    <div class="col-3">
                        <input type="checkbox" class="form-check-input header_option" name='boedata[]' value="hawb_number" id="hawb_number">
                        <h6> Current Status Of The Cbe</h6>
                    </div>

                </div>

                <hr>

                <div class="form-check d-flex row">
                    <div class="col-3">
                        <input type="checkbox" class="form-check-input header_option" name='boedata[]' value="unique_consignment_number" id="unique_consignment_number">
                        <h6>Consignment Number </h6>
                    </div>

                    <div class="col-3">
                        <input type="checkbox" class="form-check-input header_option" name='boedata[]' value="name_of_consignor" id="name_of_consignor">
                        <h6>Name Of Consignor </h6>
                    </div>

                    <div class="col-3">
                        <input type="checkbox" class="form-check-input header_option" name='boedata[]' value="address_of_consignor" id="address_of_consignor">
                        <h6>Consignor Add. </h6>
                    </div>

                    <div class="col-3">
                        <input type="checkbox" class="form-check-input header_option" name='boedata[]' value="name_of_consignee" id="name_of_consignee">
                        <h6>Name Of Consignee</h6>
                    </div>

                </div>

                <hr>

                <div class="form-check d-flex row">

                    <div class="col-3">
                        <input type="checkbox" class="form-check-input header_option" name='boedata[]' value="address_of_consignee" id="address_of_consignee">
                        <h6>Consignee Add.</h6>
                    </div>

                    <div class="col-3">
                        <input type="checkbox" class="form-check-input header_option" name='boedata[]' value="import_export_code" id="import_export_code">
                        <h6>Import Export Code</h6>
                    </div>

                    <div class="col-3">
                        <input type="checkbox" class="form-check-input header_option" name='boedata[]' value="iec_branch_code" id="iec_branch_code">
                        <h6>IEC Branch Code</h6>
                    </div>

                    <div class="col-3">
                        <input type="checkbox" class="form-check-input header_option" name='boedata[]' value="special_request" id="special_request">
                        <h6>Special Request</h6>
                    </div>

                </div>

                <hr>

                <div class="form-check d-flex row">

                    <div class="col-3">
                        <input type="checkbox" class="form-check-input header_option" name='boedata[]' value="no_of_packages" id="no_of_packages">
                        <h6>No Of Packeges</h6>
                    </div>

                    <div class="col-3">
                        <input type="checkbox" class="form-check-input header_option" name='boedata[]' value="gross_weight" id="gross_weight">
                        <h6>Gross Weight </h6>
                    </div>

                    <div class="col-3">
                        <input type="checkbox" class="form-check-input header_option" name='boedata[]' value="net_weight" id="net_weight">
                        <h6>Net Weight</h6>
                    </div>

                    <div class="col-3">
                        <input type="checkbox" class="form-check-input header_option" name='boedata[]' value="assessable_value" id="assessable_value">
                        <h6>Assessable Value</h6>
                    </div>

                </div>

                <hr>

                <div class="form-check d-flex row">

                    <div class="col-3">
                        <input type="checkbox" class="form-check-input header_option" name='boedata[]' value="duty_rs" id="duty_rs">
                        <h6>Duty Rs</h6>
                    </div>

                    <div class="col-3">
                        <input type="checkbox" class="form-check-input header_option" name='boedata[]' value="invoice_value" id="invoice_value">
                        <h6>Invoice Value</h6>
                    </div>

                    <div class="col-3">
                        <input type="checkbox" class="form-check-input header_option" name='boedata[]' value="case_of_crn" id="case_of_crn">
                        <h6>Case of CRN</h6>
                    </div>

                    <div class="col-3">
                        <input type="checkbox" class="form-check-input header_option" name='boedata[]' value="kyc_document" id="kyc_document">
                        <h6>Kyc Document</h6>
                    </div>

                    
                </div>

                <hr>
                
                <div class="form-check d-flex row">

                    <div class="col-3">
                        <input type="checkbox" class="form-check-input header_option" name='boedata[]' value="kyc_no" id="kyc_no">
                        <h6>Kyc No</h6>
                    </div>

                    <div class="col-3">
                        <input type="checkbox" class="form-check-input header_option" name='boedata[]' value="state_code" id="state_code">
                        <h6>State Code</h6>
                    </div>

                    <div class="col-3">
                        <input type="checkbox" class="form-check-input header_option" name='boedata[]' value="interest" id="interest">
                        <h6>Interest</h6>
                    </div>

                    <div class="col-3">
                        <input type="checkbox" class="form-check-input header_option" name='boedata[]' value="government_or_non_gov" id="government_or_non_gov">
                        <h6>Gov. or Non Gov.</h6>
                    </div>
                    
                </div>

                <hr>
                
                <div class="form-check d-flex row">
                    
                    <div class="col-3">
                        <input type="checkbox" class="form-check-input header_option" name='boedata[]' value="ad_code" id="ad_code">
                        <h6>AD Code</h6>
                    </div>

                    <div class="col-3">
                        <input type="checkbox" class="form-check-input header_option" name='boedata[]' value="license_type" id="license_type">
                        <h6>License Type</h6>
                    </div>

                    <div class="col-3">
                        <input type="checkbox" class="form-check-input header_option" name='boedata[]' value="license_number" id="license_number">
                        <h6>License Number</h6>
                    </div>

                    <div class="col-3">
                        <input type="checkbox" class="form-check-input header_option" name='boedata[]' value="ctsh" id="ctsh">
                        <h6>CTSH</h6>
                    </div>

                    
                </div>

                <hr>
                
                <div class="form-check d-flex row">

                    <div class="col-3">
                        <input type="checkbox" class="form-check-input header_option" name='boedata[]' value="cetsh" id="cetsh">
                        <h6>CETSH</h6>
                    </div>
    
                    <div class="col-3">
                        <input type="checkbox" class="form-check-input header_option" name='boedata[]' value="countryof_origin" id="countryof_origin">
                        <h6>Country Of Origin</h6>
                    </div>
    
                    <div class="col-3">
                        <input type="checkbox" class="form-check-input header_option" name='boedata[]' value="descriptionof_goods" id="descriptionof_goods">
                        <h6>Description of Goods</h6>
                    </div>

                    <div class="col-3">
                        <input type="checkbox" class="form-check-input header_option" name='boedata[]' value="nameof_manufacturer" id="nameof_manufacturer">
                        <h6>Name of Manufacturer</h6>
                    </div>

                    
                </div>

                <hr>
                
                <div class="form-check d-flex row">
                    
                    <div class="col-3">
                        <input type="checkbox" class="form-check-input header_option" name='boedata[]' value="address_of_manufacturer" id="address_of_manufacturer">
                        <h6>Add. of Manufacturer</h6>
                    </div>
    
                    <div class="col-3">
                        <input type="checkbox" class="form-check-input header_option" name='boedata[]' value="numberof_packages" id="numberof_packages">
                        <h6>Number of Packages</h6>
                    </div>
    
                    <div class="col-3">
                        <input type="checkbox" class="form-check-input header_option" name='boedata[]' value="markson_packages" id="markson_packages">
                        <h6>Markson Packages </h6>
                    </div>

                    <div class="col-3">
                        <input type="checkbox" class="form-check-input header_option" name='boedata[]' value="unitof_measure" id="unitof_measure">
                        <h6>Unit of Measure</h6>
                    </div>

                    
                </div>

                <hr>
                
                <div class="form-check d-flex row">
                    
                    <div class="col-3">
                        <input type="checkbox" class="form-check-input header_option" name='boedata[]' value="quantity" id="quantity">
                        <h6>Quantity </h6>
                    </div>
    
                    <div class="col-3">
                        <input type="checkbox" class="form-check-input header_option" name='boedata[]' value="invoice_number" id="invoice_number">
                        <h6>Invoice Number</h6>
                    </div>
    
                    <div class="col-3">
                        <input type="checkbox" class="form-check-input header_option" name='boedata[]' value="unit_price" id="unit_price">
                        <h6>Unit Price </h6>
                    </div>

                    <div class="col-3">
                        <input type="checkbox" class="form-check-input header_option" name='boedata[]' value="currencyof_unit_price" id="currencyof_unit_price">
                        <h6>Currency of Unit Price</h6>
                    </div>

                    
                </div>

                <hr>
                
                <div class="form-check d-flex row">
                    
                    <div class="col-3">
                        <input type="checkbox" class="form-check-input header_option" name='boedata[]' value="currencyof_invoice" id="currencyof_invoice">
                        <h6>Currency of Invoice</h6>
                    </div>
    
                    <div class="col-3">
                        <input type="checkbox" class="form-check-input header_option" name='boedata[]' value="rateof_exchange" id="rateof_exchange">
                        <h6>Rate of Exchange</h6>
                    </div>
    
                    <div class="col-3">
                        <input type="checkbox" class="form-check-input header_option" name='boedata[]' value="invoice_term" id="invoice_term">
                        <h6>Invoice Term </h6>
                    </div>

                    <div class="col-3">
                        <input type="checkbox" class="form-check-input header_option" name='boedata[]' value="landing_charges" id="landing_charges">
                        <h6>Landing Charges</h6>
                    </div>

                    
                </div>

                <hr>
                
                <div class="form-check d-flex row">
                    
                    <div class="col-3">
                        <input type="checkbox" class="form-check-input header_option" name='boedata[]' value="insurance" id="insurance">
                        <h6>Insurance</h6>
                    </div>
    
                    <div class="col-3">
                        <input type="checkbox" class="form-check-input header_option" name='boedata[]' value="freight" id="freight">
                        <h6>Freight </h6>
                    </div>
    
                    <div class="col-3">
                        <input type="checkbox" class="form-check-input header_option" name='boedata[]' value="discount_amount" id="discount_amount">
                        <h6>Discount Amount </h6>
                    </div>

                    <div class="col-3">
                        <input type="checkbox" class="form-check-input header_option" name='boedata[]' value="currencyof_discount" id="currencyof_discount">
                        <h6>Currency of Discount </h6>
                    </div>

                    
                </div>

                <hr>
                
                <div class="form-check d-flex row">
                    
                    <div class="col-3">
                        <input type="checkbox" class="form-check-input header_option" name='boedata[]' value="airlines" id="airlines">
                        <h6>Airlines </h6>
                    </div>
    
                    <div class="col-3">
                        <input type="checkbox" class="form-check-input header_option" name='boedata[]' value="flight_no" id="flight_no">
                        <h6>Flight Number </h6>
                    </div>
    
                    <div class="col-3">
                        <input type="checkbox" class="form-check-input header_option" name='boedata[]' value="airport_of_arrival" id="airport_of_arrival">
                        <h6>Airport of Arrival </h6>
                    </div>

                    <div class="col-3">
                        <input type="checkbox" class="form-check-input header_option" name='boedata[]' value="first_port_of_arrival" id="first_port_of_arrival">
                        <h6>First Port of Arrival </h6>
                    </div>

                    
                </div>

                <hr>
                
                <div class="form-check d-flex row">
                    
                    <div class="col-3">
                        <input type="checkbox" class="form-check-input header_option" name='boedata[]' value="date_of_arrival" id="date_of_arrival">
                        <h6>Date of Arrival </h6>
                    </div>
    
                    <div class="col-3">
                        <input type="checkbox" class="form-check-input header_option" name='boedata[]' value="time_of_arrival" id="time_of_arrival">
                        <h6>Time of Arrival </h6>
                    </div>
    
                    <div class="col-3">
                        <input type="checkbox" class="form-check-input header_option" name='boedata[]' value="notification_details" id="notification_details">
                        <h6>Notification Details </h6>
                    </div>

                    <div class="col-3">
                        <input type="checkbox" class="form-check-input header_option" name='boedata[]' value="charge_details" id="charge_details">
                        <h6>Charge Details </h6>
                    </div>

                </div>

                <hr>
                
                <div class="form-check d-flex row">
                    
                    <div class="col-3">
                        <input type="checkbox" class="form-check-input header_option" name='boedata[]' value="duty_details" id="duty_details">
                        <h6>Duty Details </h6>
                    </div>
    
                    <div class="col-3">
                        <input type="checkbox" class="form-check-input header_option" name='boedata[]' value="challan_number" id="challan_number">
                        <h6>Challan Number </h6>
                    </div>
    
                    <div class="col-3">
                        <input type="checkbox" class="form-check-input header_option" name='boedata[]' value="total_amount" id="total_amount">
                        <h6>Total Amount </h6>
                    </div>

                    <div class="col-3">
                        <input type="checkbox" class="form-check-input header_option" name='boedata[]' value="challan_date" id="challan_date">
                        <h6>Challan Date </h6>
                    </div>

                </div>

                <hr>

                <div class="form-check d-flex row">

                    <div class="col-3">
                        <input type="checkbox" class="form-check-input header_option" name='boedata[]' value="payment_details" id="payment_details">
                        <h6>Payment Details </h6>
                    </div>

                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary button_close" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="boe_export_csv">Export to Csv</button>
            </div>

        </div>

    </div>

</div>


<!-- end modal -->


<div class="row">

</div>
<br>
<table class="table table-bordered yajra-datatable table-striped" id="report_table">
    <thead>
        <tr>
            <td>S/N</td>
            <!-- <td>Company Name</td> -->
            <td>AWB No.</td>
            <td>Reg No</td>
            <td>Authorized Courier</td>
            <td>Name of Consignor</td>
            <td>Name of Consignee</td>
            <td>Rate</td>
            <td>Duty</td>
            <td>SW Srchrg</td>
            <td>Insurance</td>
            <td>IGST</td>
            <td>Total</td>
            <td>Interest</td>
            <!-- <td>CBX II No</td> -->
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
    // $('#export_boe').on('click', function(e) {
    //     e.preventDefault();
    //     $('#boe_export_filter').submit();
    // });

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
                // {
                //     data: 'cbe_number',
                //     name: 'cbe_number',
                //     orderable: false,
                //     searchable: false
                // },
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

    // open modal
    $(".exportboe_modal_open").on('click', function() {
        $('#exportboe').modal('show');
    });

    $(".close").on('click', function() {
        $('#exportboe').modal('hide');
    });

    $(".button_close").on('click', function() {
        $('#exportboe').modal('hide');
    });

    $('.all').change(function() {
        if ($('.all').is(':checked')) {
            $(".header_option").prop("checked", true);
        } else {
            $(".header_option").prop("checked", false);
        }
    });

    $('.header_option').change(function() {
        let select_header = [];
        let count = 0;

        $("input[name='boedata[]']:checked").each(function() {
            count++;
        });

        if (count === 65) {
            $(".all").prop("checked", true);
        } else {
            $(".all").prop("checked", false);
        }
    });

    $('#boe_export_csv').on('click', function() {
        let date_of_arrival = $('#date_of_arrival').val();
        let company = $('#company').val();
        let challan_date = $('#challan_date').val();
        let upload_date = $('#upload_date').val();
        // alert(company);
        // return false;
        let select_header = [];
        let count = 0;
        $("input[name='boedata[]']:checked").each(function() {
            if (count == 0) {

                select_header += $(this).val();
            } else {
                select_header += '=!' + $(this).val();
            }
            count++;
        });
        if (count == 0) {
            $('#exportboe').modal('show');
            alert('Please Select Header');
            // $('.progress_bar').hide();
        }
        else {
            select_header += '=!' +company + '=!' +date_of_arrival + '=!' +challan_date+ '=!'+upload_date;
            $('#exportboe').modal('hide');
            console.log(select_header);
            $.ajax({
                method: 'post',
                url: '/BOE/Export/filter',
                data: {
                    "_token": "{{ csrf_token() }}",
                    "_method": 'post',
                    'selected': select_header,
                },
                success: function(response) {

                    if(response.success) {
                        window.location = '/BOE/Download';
                    } else if(response.error) {
                        alert(response.error);
                    }

                    // $('.progress_bar').hide();
                    // yajra_table.ajax.reload();
                }
            })
        }

    }).get();
</script>
@stop