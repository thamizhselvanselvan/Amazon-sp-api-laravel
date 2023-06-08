@extends('adminlte::page')

@section('title', 'Packet Booking')
@section('css')
<link rel="stylesheet" href="/css/styles.css">
<style>
    .align {
        background: wheat;
        border-radius: 10px;
        padding: 15px;
        display: grid;
        grid-template-columns: 1fr 1fr 1fr 1fr;
        width: 70%;
        margin: auto;
        grid-gap: 15px;
        margin-top: 20px
    }

    .form-group {
        margin-bottom: 0px;
    }

    .alert {
        padding: 0.45rem 1.25rem;
        font-size: 14px;
    }

    .card-body {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr 1fr;
        gap: 10px;
    }

    .card-body .form-group {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        align-items: center;
    }

    .card .card-header a {
        background: #1b86f5;
        color: white;
    }

    .card .card-header a::after {
        content: "";
        width: 10px;
        height: 10px;
        border-top: 2px solid white;
        border-right: 2px solid white;
        position: absolute;
        right: 28px;
        transform: rotate(133deg);
        top: 12px;
    }

    .card .card-header a:collapse::after {
        transform: rotate(13deg);
    }

    .form-control {
        height: calc(1.9rem + 2px);
    }

    .card-body .form-group .input-group {
        grid-column-end: 4;
        grid-column-start: 2;
    }

    .card-body .form-group label {
        font-size: 14px;
        text-align: end;
        padding-right: 5px;

    }
</style>
@stop
@section('content_header')
<div class="row">

    <!-- <div class="col-6">
                                <div class="alert alert-warning" role="alert">
                                    Please Choose<b> Source-Destination </b>Before Creating Or Editing ...
                                </div>
                            </div> -->
    <!-- <h2 class="mb-4 text-right col">
                                        <a href="{{ Route('shipntrack.forwarder.template') }}">
                                <x-adminlte-button label="Download Template" theme="primary" icon="fas fa-file-download" class="btn-sm" />
                                </a>
                                <a href="{{ Route('shipntrack.forwarder.upload') }}">
                                    <x-adminlte-button label="Add New Records" theme="primary" icon="fas fa-plus" class="btn-sm" />
                                </a>
                                <a href="{{ Route('shipntrack.missing.find') }}">
                                    <x-adminlte-button label="Export Order ID's And AWB Number" theme="primary" icon="fas fa-file-upload" class="btn-sm" />
                                </a>
                                </h2> -->

    {{-- <div class="col-2 temp d-none">
        <x-adminlte-button label="Edit Shipment" type="edit" name="edit" theme="success" icon="fa fa-edit" class="float-right" id="edit_shipment" />
    </div>
    <div class="col">
        <a href="{{ route('shipntrack.courier.track') }}">
    <x-adminlte-button label="Get Details" type="submit" name="GetDetails" theme="primary" icon="fa fa-refresh" class="float-right" id="dashboard_refresh" />
    </a>
</div> --}}
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

        <div class="alert_display">
            @if ($message = Session::get('error'))
            <div class="alert alert-danger alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $message }}</strong>
            </div>
            @endif
        </div>
    </div>
</div>
<form action="{{ Route('shipntrack.forwarder.store.forwarder') }}" method="post" id="admin_user">
    @csrf

    <div class="row">
        <div class="col-2">
            <div style="margin-top: -1.0rem;">
                <!-- <x-adminlte-select name="destination" label="Source-Destination" id="destination" required>
                        <option value="0">Source-Destination</option>
                        @foreach ($destinations as $destination)
                            <option value={{ $destination['destination'] }}>
                                {{ $destination['source'] . '-' . $destination['destination'] }}
                            </option>
                        @endforeach
                    </x-adminlte-select> -->

                <x-adminlte-select name="destination" label="Source-Destination" id="destination">
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
        </div>
        <div class="col-4">
            <h2 class="m-0 text-dark text-center col">Packet Booking</h2>
        </div>
    </div>


    <div class="container-fluid display pt-3 d-none ">
        <div id="accordion">

            <div class="card">
                <div class="card-header p-0">
                    <a class="d-flex px-4 py-1" data-bs-toggle="collapse" href="#collapseOne">
                        Packet Booking :
                    </a>

                </div>
                <div id="collapseOne" class="collapse show" data-bs-parent="#accordion">
                    <div class="card-body py-1">
                        <x-adminlte-input label="AWB No :" name="awb_no" type="text" id="system_gen" placeholder="AWB No." />
                        <x-adminlte-input label="Order No :" name="order_id" type="text" placeholder="Enter Order No:" value="{{ old('order_id') }}" required />
                        <x-adminlte-input label="Item Number :" name="item_no" type="text" placeholder="Order Item Number" value="{{ old('item_no') }}" required />
                        <x-adminlte-input label="Reference ID:" name="reference_id" type="text" placeholder="Sytem Generated" value="{{ old('reference_id') }}" id="reference" required />
                        <x-adminlte-input label="Booking Date:" name="date" type="date" placeholder="date..." value="{{ old('date') }}" required />
                        <x-adminlte-input label="Purchase ID:" name="purchase_tracking_id" type="test" placeholder="Purchase Tracking ID" value="{{ old('purchase_tracking_id') }}" required />
                    </div>
                    <div class="">
                        <div class="card-header p-0">
                            <a class="collapsed d-flex px-4 py-1" data-bs-toggle="collapse" href="#collapseTwo">
                                Consignor Details :
                            </a>
                        </div>
                        <div id="collapseTwo" class="collapse show" data-bs-parent="#accordion">
                            <div class="card-body py-1">
                                <x-adminlte-input label="Consignor :" name="cnr_consignor" type="text" placeholder="Consignor" value="{{ old('cnr_consignor') }}" required />
                                <x-adminlte-input label="CPerson :" name="cnr_cperson" type="text" placeholder="Contact Person" value="{{ old('cnr_cperson') }}" />
                                <x-adminlte-input label="Address1 :" name="cnr_address1" type="text" placeholder="Enter Address1" value="{{ old('cnr_address1') }}" required />
                                <x-adminlte-input label="Address2 :" name="cnr_address2" type="text" placeholder="Enter Address2" value="{{ old('cnr_address2') }}" required />
                                <x-adminlte-input label="Pin Code :" name="cnr_pincode" type="text" placeholder="Enter Pin Code" value="{{ old('cnr_pincode') }}" required />
                                <x-adminlte-input label="Country :" name="cnr_country" type="text" placeholder="Enter Country" value="{{ old('cnr_country') }}" required />
                                <x-adminlte-input label="State :  " name="cnr_state" type="text" placeholder="Enter State" value="{{ old('cnr_state') }}" required />
                                <x-adminlte-input label="City :" name="cnr_city" type="text" placeholder="Enter City" value="{{ old('cnr_city') }}" required />
                                <x-adminlte-input label="Mobile No :" name="cnr_mobile_no" type="text" placeholder="Enter Mobile No" value="{{ old('cnr_mobile_no') }}" required />
                            </div>
                        </div>
                    </div>
                    <div class="">
                        <div class="card-header p-0">
                            <a class="collapsed d-flex px-4 py-1 " data-bs-toggle="collapse" href="#collapseThree">
                                Consignee Details
                            </a>
                        </div>
                        <div id="collapseThree" class="collapse show" data-bs-parent="#accordion">
                            <div class="card-body py-1">
                                <x-adminlte-input label="Consignee :" name="cne_consignee" type="text" placeholder="Consignee" value="{{ old('cne_consignee') }}" required />
                                <x-adminlte-input label="CPerson :" name="cne_cperson" type="text" placeholder="Contact Person" value="{{ old('cne_cperson') }}" />
                                <x-adminlte-input label="Address1 :" name="cne_address1" type="text" placeholder="Enter Address1" value="{{ old('cne_address1') }}" required />
                                <x-adminlte-input label="Address2 :" name="cne_address2" type="text" placeholder="Enter Address2" value="{{ old('cne_address2') }}" required />
                                <x-adminlte-input label="Pin Code :" name="cne_pincode" type="text" placeholder="Enter Pin Code" value="{{ old('cne_pincode') }}" required />
                                <x-adminlte-input label="Country :" name="cne_country" type="text" placeholder="Enter Country" value="{{ old('cne_country') }}" required />
                                <x-adminlte-input label="State :  " name="cne_state" type="text" placeholder="Enter State" value="{{ old('cne_state') }}" required />
                                <x-adminlte-input label="City :" name="cne_city" type="text" placeholder="Enter City" value="{{ old('cne_city') }}" required />
                                <x-adminlte-input label="Mobile No :" name="cne_mobile_no" type="text" placeholder="Enter Mobile No" value="{{ old('cne_mobile_no') }}" required />
                            </div>
                        </div>
                    </div>
                    <div class="">
                        <div class="card-header p-0">
                            <a class="collapsed d-flex px-4 py-1 " data-bs-toggle="collapse" href="#collapsefour">
                                Packet Details :
                            </a>
                        </div>
                        <div id="collapsefour" class="collapse show" data-bs-parent="#accordion">
                            <div class="card-body py-1">
                                <x-adminlte-input label="Packet Type :" name="packet_type" type="text" placeholder="Packet Type" value="{{ old('packet_type') }}" required />
                                <x-adminlte-input label="Price :" name="price" type="text" placeholder=" Enter Price" value="{{ old('price') }}" required />
                                <x-adminlte-input label="Currency :" name="currency" type="text" placeholder=" Enter Currency" value="{{ old('currency') }}" />
                                <x-adminlte-input label="Invoice No :" name="invoice_no" type="text" placeholder=" Enter Invoice No" value="{{ old('invoice_no') }}" required />
                                <x-adminlte-input label="Taxable Value :" name="tax_value" type="text" placeholder=" Enter Taxable Value" value="{{ old('tax_value') }}" required />
                                <x-adminlte-input label="Total Including Taxes :" name="total_inc_tax" type="text" placeholder=" Total Including Taxes" value="{{ old('total_inc_tax') }}" required required />
                                <x-adminlte-input label="Grand Total :" name="total" type="text" placeholder=" Grand Total" value="{{ old('total') }}" required />
                                <x-adminlte-input label="Packet Desc :" name="pkt_name" type="text" placeholder=" Enter Packet Description" value="{{ old('pkt_name') }}" required />
                                <x-adminlte-input label="Quantity :" name="qty" type="text" placeholder=" Enter Quantity" value="{{ old('qty') }}" required />
                                <x-adminlte-input label="PCS :" name="pieces" type="text" placeholder="Enter pieces" value="{{ old('pieces') }}" required />
                                <x-adminlte-input label="Dimension  :" name="dimension" type="text" placeholder="dimension" value="{{ old('dimension') }}" required />
                                <x-adminlte-input label="Actual Weight  :" name="actual_weight" type="text" placeholder="Actual Weight" value="{{ old('actual_weight') }}" required />
                                <x-adminlte-input label="Charged Weight  :" name="charged_weight" type="text" placeholder="Charged Weight" value="{{ old('charged_weight') }}" required />

                            </div>
                        </div>
                    </div>

                    <!-- <div class="card">
                                                <div class="card-header p-0">
                                                    <a class="collapsed d-flex px-4 py-1 " data-bs-toggle="collapse" href="#collapsesix">
                                                        Forwarder Details :
                                                    </a>
                                                </div>
                                                <div id="collapsesix" class="collapse show" data-bs-parent="#accordion">
                                                    <div class="card-body py-1">
                                                        <x-adminlte-select label="Forwarder 1:" name="forwarder1" id="forwarder_info_1" value="{{ old('forwarder2') }}" required>
                                                            <option value=''> Forwarder 1</option>
                                                        </x-adminlte-select>
                                                        <x-adminlte-select label="Forwarder 2:" name="forwarder2" id="forwarder_info_2" value="{{ old('forwarder2') }}">
                                                            <option value=''> Forwarder 2</option>
                                                        </x-adminlte-select>
                                                        <x-adminlte-select label="Forwarder 3:" name="forwarder3" id="forwarder_info_3" value="{{ old('forwarder3') }}">
                                                            <option value=''> Forwarder 3</option>
                                                        </x-adminlte-select>
                                                        <x-adminlte-select label="Forwarder 4:" name="forwarder4" id="forwarder_info_4" value="{{ old('forwarder2') }}">
                                                            <option value=''> Forwarder 4</option>
                                                        </x-adminlte-select>
                                                        <x-adminlte-input label="Forwarder 1 AWB :" name="forwarder_1_awb" type="text" placeholder="Forwarder 1 AWB " value="{{ old('forwarder_1_awb') }}"  required />
                                                        <x-adminlte-input label="Forwarder 2 AWB :" name="forwarder_2_awb" type="text" placeholder="Forwarder 2 AWB " value="{{ old('forwarder_2_awb') }}"  />
                                                        <x-adminlte-input label="Forwarder 3 AWB :" name="forwarder_3_awb" type="text" placeholder="Forwarder 3 AWB " value="{{ old('forwarder_3_awb') }}"  />
                                                        <x-adminlte-input label="Forwarder 4 AWB :" name="forwarder_4_awb" type="text" placeholder="Forwarder 4 AWB " value="{{ old('forwarder_4_awb') }}"  />

                                                    </div>
                                                </div>

                                            </div> -->
                    <div class="">
                        <div class="card-header p-0">
                            <a class="collapsed d-flex px-4 py-1 " data-bs-toggle="collapse" href="#collapseseven">
                                Shipping Details :
                            </a>
                        </div>
                        <div id="collapseseven" class="collapse show" data-bs-parent="#accordion">
                            <div class="card-body py-1">
                                <x-adminlte-input label="SKU :" name="sku" type="text" placeholder=" Enter SKU" value="{{ old('sku') }}" required />
                                <x-adminlte-input label="HSN Code  :" name="hsn" type="text" placeholder=" Enter HSN No" value="{{ old('hsn') }}" required />
                                <x-adminlte-input label="channel :" name="channel" type="text" placeholder=" Enter channel" value="{{ old('channel') }}" required />
                                <x-adminlte-input label="Shipped By :" name="shipped_by" type="text" placeholder=" Enter Shipped By" value="{{ old('ship_by') }}" required />
                                <x-adminlte-input label="ARN NO :" name="arn_no" type="text" placeholder=" Enter ARN NO" value="{{ old('arn_no') }}" required />
                                <x-adminlte-input label="Store Name :" name="store" type="text" placeholder=" Enter Store Name" value="{{ old('store') }}" />
                                <x-adminlte-input label="Store Address :" name="store_address" type="text" placeholder=" Enter store_address" value="{{ old('store_address') }}" required />
                                <x-adminlte-input label="Bill To Name :" name="bill_name" type="text" placeholder=" Enter Bill To Name" value="{{ old('bill_name') }}" required />
                                <x-adminlte-input label="Billing Address :" name="bill_address" type="text" placeholder=" Enter Billing Address" value="{{ old('bill_name') }}" required />
                                <x-adminlte-input label="Ship To Name :" name="ship_name" type="text" placeholder=" Enter Ship To Name" value="{{ old('ship_name') }}" required />
                                <x-adminlte-input label="Shipping Address :" name="ship_address" type="text" placeholder=" Enter shipping Address" value="{{ old('ship_address') }}" required />
                            </div>
                        </div>

                    </div>
                    <div class="d-flex justify-content-center align-item-center py-4">
                        <div>
                            <div>
                                <x-adminlte-button label="Submit" theme="info" icon="fas fa-save" type="submit" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <!-- <div class="align">
                                    <div>
                                        <x-adminlte-input label="Enter Reference ID:" name="reference" id="refrence" type="text"
                                            placeholder="RefrenceID..." value="{{ old('reference') }}" />
                                    </div>
                                    <div>
                                        <x-adminlte-input label="Consignor :" name="consignor" type="text" placeholder="Consignor"
                                            value="{{ old('consignor') }}" autocomplete="off" />
                                    </div>
                                    <div>
                                        <x-adminlte-input label="Consignee :" name="consignee" type="text" placeholder="Consignee"
                                            value="{{ old('consignee') }}" autocomplete="off" />
                                    </div>

                                    <div></div>
                                    <div>
                                        <x-adminlte-select label="Select Forwarder 1:" name="forwarder1" id="forwarder_info_1"
                                            value="{{ old('forwarder2') }}">
                                            <option value=''> Forwarder 1</option>
                                        </x-adminlte-select>
                                    </div>
                                    <div>
                                        <x-adminlte-select label="Select Forwarder 2:" name="forwarder2" id="forwarder_info_2"
                                            value="{{ old('forwarder2') }}">
                                            <option value=''> Forwarder 2</option>
                                        </x-adminlte-select>
                                    </div>
                                    <div>
                                        <x-adminlte-select label="Select Forwarder 3:" name="forwarder3" id="forwarder_info_3"
                                            value="{{ old('forwarder3') }}">
                                            <option value=''> Forwarder 3</option>
                                        </x-adminlte-select>
                                    </div>
                                    <div>
                                        <x-adminlte-select label="Select Forwarder 4:" name="forwarder4" id="forwarder_info_4"
                                            value="{{ old('forwarder2') }}">
                                            <option value=''> Forwarder 4</option>

                                        </x-adminlte-select>
                                    </div>

                                    <div>
                                        <x-adminlte-input label="Forwarder 1 AWB :" name="forwarder_1_awb" type="text"
                                            placeholder="Forwarder 1 AWB " value="{{ old('forwarder_1_awb') }}" autocomplete="off" />
                                    </div>
                                    <div>
                                        <x-adminlte-input label="Forwarder 2 AWB :" name="forwarder_2_awb" type="text"
                                            placeholder="Forwarder 2 AWB " value="{{ old('forwarder_2_awb') }}" autocomplete="off" />
                                    </div>
                                    <div>
                                        <x-adminlte-input label="Forwarder 3 AWB :" name="forwarder_3_awb" type="text"
                                            placeholder="Forwarder 3 AWB " value="{{ old('forwarder_3_awb') }}" autocomplete="off" />
                                    </div>
                                    <div>
                                        <x-adminlte-input label="Forwarder 4 AWB :" name="forwarder_4_awb" type="text"
                                            placeholder="Forwarder 4 AWB " value="{{ old('forwarder_4_awb') }}" autocomplete="off" />
                                    </div>

                                    <div>
                                        <div>
                                            <x-adminlte-button label=" Submit" theme="info" icon="fas fa-save" type="submit" />
                                        </div>
                                    </div>

                                </div> -->
</form>

@stop

@section('js')
<script type="text/javascript">
    $("#destination").on('change', function() {
        $(".display").removeClass("d-none")
    });


    $(document).ready(function() {
        $("#reference").prop('disabled', true);
    });

    $("#destination").on('change', function(e) {
        $(".temp").removeClass("d-none")
        let destination = $(this).val();
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
                    $('#forwarder_info_1').empty();
                    $('#forwarder_info_2').empty();
                    $('#forwarder_info_3').empty();
                    $('#forwarder_info_4').empty();
                    let forwarder_data = "<option value='' >" + 'Select Forwarder' + "</option>";
                    $.each(result, function(i, result) {
                        forwarder_data += "<option value='" + result.id + "'>" + result
                            .user_name +
                            "</option>";
                    });
                    $('#forwarder_info_1').append(forwarder_data);
                    $('#forwarder_info_2').append(forwarder_data);
                    $('#forwarder_info_3').append(forwarder_data);
                    $('#forwarder_info_4').append(forwarder_data);
                }
            });
        }
    });




    $("#edit_shipment").on('click', function(e) {

        let destination = $('#destination').val();
        if (destination == '') {
            alert('Source-Destination is Required');
            return false;
        }
        window.location = "/shipntrack/shipment/edit/" + destination;

    });
</script>
@stop