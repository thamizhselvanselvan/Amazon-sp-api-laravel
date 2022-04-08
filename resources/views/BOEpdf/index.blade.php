@extends('adminlte::page')

@section('title', 'BOE Master')

@section('content_header')

    <h1 class="m-0 text-dark">Bill OF Entry</h1>
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
    <h2 class="mb-4">
        <a href="uplod">
            <x-adminlte-button label=" Pdf Upload" theme="primary" icon="fas fa-file-export" />
        </a>
    </h2>
    <table class="table table-bordered yajra-datatable table-striped">
        <thead>
            <tr>
                <th>ID Pimary</th>
                <th>Current status of CBE</th>
                <th>courier registration number</th>
                <th>cbe number </th>
                <th>name of the authorized courier</th>
                <th>address of authorized courier</th>
                <th> airport of shipment </th>
                <th>country of exportation </th>
                <th>hawb number </th>
                <th>unique consignment number </th>
                <th>name of consignor</th>
                <th>address of consignor </th> 
                <th>name of consignee </th>
                <th>address of consignee </th>
                <th>import export code </th>
                <th>iec branch code </th>
                <th>special request</th> 
                <th>no of packages </th>
                <th>gross weight </th>
                <th>net weight </th>
                <th>assessable value </th>
                <th>duty rs </th>
                <th>invoice value </th>
                <th>case of crn</th>
                <th>kyc document </th>
                <th>kyc no </th>
                <th>state code </th>
                <th>government or non gov </th>
                <th>ad code </th>
                <th>license type </th>
               <th>license_number </th>
                 <th>ctsh </th>
                <th>cetsh </th>
                 {{--<th>country of origin </th>
                 <th>descriptionof goods </th>
                <th>name of manufacturer </th> --}}
                {{--<th>address of manufacturer </th>
                <th>numberof packages </th>
                <th>markson packages </th>
                <th>unitofmeasure </th>
                <th>quantity </th>
                <th>invoice number </th>
                <th>unit price </th>
                <th>currencyof unit price </th>
                <th>currencyof invoice </th>
                <th>rateof exchange </th>
                <th>invoice term </th>
                <th>landing charges </th>
                <th>insurance </th>
                <th>freight </th>
                <th>discount amount </th>
                <th>currencyof discount </th>
                <th>airlines </th>
                <th>flight no </th>
                <th>airport of arrival </th>
                <th>first port of arrival </th>
                <th>date of arrival </th>
                <th>time of arrival </th>
                <th>notification details </th>
                <th>charge details</th>
                <th>duty details </th>
                <th>payment details</th>  --}}

            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>




@stop

@section('js')

    <script type="text/javascript">
        let yajra_table = $('.yajra-datatable').DataTable({

            processing: true,
            serverSide: true,

            ajax: "{{ url('BOE/index') }}",
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'current_status_of_the_cbe',
                    name: 'current_status_of_the_cbe'
                },
                {
                    data: 'courier_registration_number',
                    name: 'courier_registration_number'
                },
                {
                    data: 'cbe_number',
                    name: 'cbe_number'
                },
                {
                    data: 'name_of_the_authorized_courier',
                    name: 'name_of_the_authorized_courier'
                },
                {
                    data: 'address_of_authorized_courier',
                    name: 'address_of_authorized_courier'
                },
                {
                    data: 'airport_of_shipment',
                    name: 'airport_of_shipment'
                },
                {
                    data: 'country_of_exportation',
                    name: 'country_of_exportation'
                },
                {
                    data: 'hawb_number',
                    name: 'hawb_number'
                },
                {
                    data: 'unique_consignment_number',
                    name: 'unique_consignment_number'
                },
                {
                    data: 'name_of_consignor',
                    name: 'name_of_consignor'
                },
                {
                    data: 'address_of_consignor',
                    name: 'address_of_consignor'
                },
                {
                    data: 'name_of_consignee',
                    name: 'name_of_consignee'
                },
                {
                    data: 'address_of_consignee',
                    name: 'address_of_consignee'
                },
                {
                    data: 'import_export_code',
                    name: 'import_export_code'
                },
                {
                    data: 'iec_branch_code',
                    name: 'iec_branch_code'
                },
                {
                    data: 'special_request',
                    name: 'special_request'
                },
                {
                    data: 'no_of_packages',
                    name: 'no_of_packages'
                },
                {
                    data: 'gross_weight',
                    name: 'gross_weight'
                },
                {
                    data: 'net_weight',
                    name: 'net_weight'
                },
                {
                    data: 'assessable_value',
                    name: 'assessable_value '
                },
                {
                    data: 'duty_rs',
                    name: 'duty_rs'
                },
                {
                    data: 'invoice_value',
                    name: 'invoice_value'
                },
                {
                    data: 'case_of_crn',
                    name: 'case_of_crn'
                },
                {
                    data: 'kyc_document',
                    name: 'kyc_document'
                },
                {
                    data: 'kyc_no',
                    name: 'kyc_no'
                },
                {
                    data: 'state_code',
                    name: 'state_code'
                },
                {
                    data: 'government_or_non_gov',
                    name: 'government_or_non_gov'
                },
                {
                    data: 'ad_code',
                    name: 'ad_code'
                },
                {
                    data: 'license_type',
                    name: 'license_type'
                },
                {
                    data: 'license_number',
                    name: 'license_number'
                },
                {
                    data: 'ctsh',
                    name: 'ctsh'
                },
                {
                    data: 'cetsh',
                    name: 'cetsh'
                },
                // {
                //     data: 'countryof_origin',
                //     name: 'countryof_origin'
                // },
                // {
                //     data: 'descriptionof_goods',
                //     name: 'descriptionof_goods'
                // },
                // {
                //     data: 'nameof_manufacturer',
                //     name: 'nameof_manufacturer'
                // },
                // {
                //     data: 'address_of_manufacturer',
                //     name: 'address_of_manufacturer'
                // },
                // {
                //     data: 'numberof_packages',
                //     name: 'numberof_packages'
                // },
                // {
                //     data: 'markson_packages',
                //     name: 'markson_packages'
                // },
                // {
                //     data: 'unitof_measure',
                //     name: 'unitof_measure'
                // },
                // {
                //     data: 'quantity',
                //     name: 'quantity'
                // },
                // {
                //     data: 'invoice_number',
                //     name: 'invoice_number'
                // },
                // {
                //     data: 'unit_price',
                //     name: 'unit_price'
                // },
                // {
                //     data: 'currencyof_unit_price',
                //     name: 'currencyof_unit_price'
                // },
                // {
                //     data: 'currencyof_invoice',
                //     name: 'currencyof_invoice'
                // },
                // {
                //     data: 'rateof_exchange',
                //     name: 'rateof_exchange'
                // },
                // {
                //     data: 'invoice_term',
                //     name: 'invoice_term'
                // },
                // {
                //     data: 'landing_charges',
                //     name: 'landing_charges'
                // },
                // {
                //     data: 'insurance',
                //     name: 'insurance'
                // },
                // {
                //     data: 'freight',
                //     name: 'freight'
                // },
                // {
                //     data: 'discount_amount',
                //     name: 'discount_amount'
                // },
                // {
                //     data: 'currencyof_discount',
                //     name: 'currencyof_discount'
                // },
                // {
                //     data: 'airlines',
                //     name: 'airlines'
                // },
                // {
                //     data: 'flight_no',
                //     name: 'flight_no'
                // },
                // {
                //     data: 'airport_of_arrival',
                //     name: 'airport_of_arrival'
                // },
                // {
                //     data: 'first_port_of_arrival',
                //     name: 'first_port_of_arrival'
                // },
                // {
                //     data: 'date_of_arrival',
                //     name: 'date_of_arrival'
                // },
                // {
                //     data: 'time_of_arrival',
                //     name: 'time_of_arrival'
                // },
                // {
                //     data: 'notification_details',
                //     name: 'notification_details'
                // },
                // {
                //     data: 'charge_details',
                //     name: 'charge_details'
                // },
                // {
                //     data: 'duty_details',
                //     name: 'duty_details'
                // },
                // {
                //     data: 'payment_details',
                //     name: 'payment_details'
                // },

            ]

        });
    </script>
@stop
