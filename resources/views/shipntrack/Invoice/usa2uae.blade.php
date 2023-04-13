@extends('adminlte::page')

@section('title', 'Invoice')

@section('content_header')
<!-- <a href="/invoice/manage" class="btn btn-sm btn-primary m-b-10 p-l-5"><i class="fa fa-arrow-left t-plus-1 fa-fw fa-sm"></i> Back</a> -->
<div class="invoice-company text-inverse f-w-600">
    <span class="pull-right hidden-print">
        <a href="javascript:void(0);" class="btn btn-sm btn-white m-b-10 p-l-5" id="Export_to_pdf"><i class="fa fa-file t-plus-1 text-danger fa-fw fa-lg"></i> Export as PDF</a>
        <a href="javascript:;" onclick="window.print()" class="btn btn-sm btn-white m-b-10 p-l-5"><i class="fa fa-print t-plus-1 fa-fw fa-lg"></i> Print</a>
    </span>
    <br>
</div>
@stop


@section('content')

<input type="hidden" id="pid" value="{{ $invoice_no }}">
<div class="container">
    <div class="col-md-12">
        <div class="invoice">
            <!-- begin invoice-company -->
            <div class="container">
                <h4 class="text-center mt-0"> <strong> TAX INVOICE </strong> </h4>
            </div>

            <div class="col-md-12 invoice-date text-left">


                <div class=" text-inverse m-t-5"><strong> INVOICE DATE: </strong> {{ $value['invoice_date'] }}</div>
                <div class=" text-inverse m-t-5"><strong> INVOICE NO.: </strong> {{ $value['invoice_no'] }}</div>
                <div class=" text-inverse m-t-5"><strong> CHANNEL: </strong> {{ $value['channel'] }}</div>
                <div class=" text-inverse m-t-5"><strong> SHIPPED BY: </strong> {{ $value['shipped_by'] }}</div>
                <div class=" text-inverse m-t-5"><strong> AWB NO.: </strong> {{ $value['awb_no'] }}</div>

                <div class=" text-inverse m-t-5"><strong> ARN NO.:&nbsp; </strong> {{ $value['arn_no'] }}</div>
                <div class=" text-inverse m-t-5"><strong> IMPORT CODE:&nbsp; </strong> 74908</div><br>
                <div class="invoice-detail">
                    <!-- Services Product -->
                </div>
            </div>
            <div class="col-md-12 invoice-date text-left">
                <div class=" text-inverse m-t-5">
                    <div class="row">
                        <div class="col"></div>
                        <div class="col"></div>
                        <div class="col">{!! $invoice_bar_code !!} {{ $value['invoice_no'] }}</div>
                    </div>
                </div>
            </div>
            <!-- end invoice-company -->
            <!-- begin invoice-header -->
            <div class="invoice-header">
                <div class="invoice-from">
                    <address class="m-t-2 m-b-5">
                        <strong class="text-inverse">
                            <h6><b>SHIPPED FROM</b></h6>
                        </strong>
                        <hr>
                        <b> {{ $value['store_name'] }} </b><br>
                        {{ $value['store_add'] }}
                    </address>
                </div>

                <div class="invoice-to">

                    <address class="m-t-2 m-b-5">
                        <strong class="text-inverse">
                            <h6><b>BILL TO </b></h6>
                        </strong>
                        <hr>
                        <b> {{ $value['bill_to_name'] }} </b><br>
                        {{ $value['bill_to_add'] }}
                    </address>
                </div>
                <div class="invoice-to">

                    <address class="m-t-2 m-b-5">
                        <strong class="text-inverse">
                            <h6><b>SHIP TO </b></h6>
                        </strong>
                        <hr>
                        <b> {{ $value['ship_to_name'] }} </b><br>
                        {{ $value['ship_to_add'] }}
                    </address>
                </div>
            </div>

            <!-- end invoice-header -->
            <!-- begin invoice-content -->
            <div class="invoice-content">
                <!-- begin table-responsive -->
                <div class="table-responsive">
                    <table class="table table-invoice table-bordered table-bordered-dark table-sm">
                        <thead>
                            <tr>
                                <th class="text-center">SR</th>
                                <th class="text-center">ITEM DESCRIPTION</th>
                                <th class="text-center">HSN</th>
                                <th class="text-center" width="10%">QTY</th>
                                <th class="text-center" width="10%">PRICE</th>
                                <th class="text-center" width="10%">TAX</th>
                                <th class="text-center" width="20%">TOTAL</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($value['product_details'] as $key => $data)
                            <tr>
                                <td class="text-center"> {{ $key + 1 }} </td>
                                <td class="text-center">
                                    @if (array_key_exists('item_description', $data))
                                    {{ $data['item_description'] }}
                                    @else
                                    N/A
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($data['hsn_code'] == '')
                                    {{ 0 }}
                                    @else
                                    {{ $data['hsn_code'] }}
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($data['qty'] == '')
                                    {{ 0 }}
                                    @else
                                    {{ $data['qty'] }}
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($data['product_price'] == '')
                                    {{ 0 }}
                                    @else
                                    {{ $data['currency'] }} {{ $data['product_price'] }}
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($data['taxable_value'] == '')
                                    {{ 0 }}
                                    @else
                                    {{ $data['taxable_value'] }}
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($data['grand_total'] == '')
                                    {{ 0 }}
                                    @else
                                    {{ $data['currency'] }} {{ $data['grand_total'] }}
                                    @endif
                                </td>
                            </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
                <!-- end table-responsive -->
                <!-- begin invoice-price -->
                <div class="invoice-price">
                    <div class="invoice-price-left">

                    </div>
                    <div class="invoice-price-right">
                        @if ($value['grand_total'] == '')
                        <small>GRAND TOTAL</small> <span class="f-w-600">{{ 0 }}</span>
                        @else
                        <small><strong> GRAND TOTAL </strong> </small> <span class="f-w-600">{{ $data['currency'] }} {{ $value['grand_total'] }}</span>
                        @endif

                    </div>
                </div>
                <!-- end invoice-price -->
                <p class=" mb-0 text-center">This is system generated invoice, it may contain only digital signature.
                </p><br><br>
                <hr class="dashed"><br><br>
                <!-- <div class="container"> -->
                <div class="row">
                    <div class="col "><img src="{{ URL::asset('/image/bombinoImage.jpg') }}" class="img-fluid rounded mx-auto d-block" alt="Bombino_express"></div>
                    <div class="col border ">
                        <!-- <div class="table-responsive"> -->
                        <div class="row">
                            <div class="col-1"></div>
                            <div class="col-10">
                                <table class="table-sm">
                                    <thead>
                                        <tr>
                                            <th>CONSIGNMENT NOTE NUMBER</th>
                                        </tr>
                                        <tr>
                                            <th> {!! $bar_code !!} {{ $value['awb_no'] }}</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                            <div class="col-1"></div>
                        </div>
                        <!-- </div> -->
                    </div>
                    <div class="col">
                        <table class="table table-invoice table-bordered table-bordered-dark table-sm text-center">
                            <thead>
                                <tr>
                                    <td colspan="2" class="text-center">SERVICE</td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="text-center">SELF</td>
                                </tr>
                                <tr>
                                    <td>ORIGIN</td>
                                    <td>DESTINATION</td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>USA</td>
                                    <td>UAE</td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
                <br>
                <table class="table table-invoice table-bordered table-bordered-dark table-sm text-left">
                    <tbody>
                        <tr>
                            <td><strong class="ml-2">SHIPPER A/C.:</strong></td>
                            <td><strong class="ml-2">DATE: </strong>&nbsp;{{ $value['invoice_date'] }} </td>
                            <td><strong class="ml-2">ONFD NO.: </strong>&nbsp;{{ $value['invoice_no'] }}</td>

                        </tr>
                    </tbody>
                </table>
                <div class="row">
                    <div class="col ">
                        <b class="bg-dark text-white mt-4 ml-2 consignor"> CONSIGNOR</b>
                        <p class=" ml-2 mt-"><b> {{ $value['store_name'] }} </b><br> {{ $value['store_add'] }} </p>
                    </div>
                    <div class="col ">
                        <b class="bg-dark text-white mt-4 ml-2 consignee"> CONSIGNEE </b>
                        <p class=" ml-2 mt-0"><b> {{ $value['ship_to_name'] }} </b><br> {{ $value['ship_to_add'] }}
                        </p>
                    </div>
                </div>
                <table class="table table-bordered table-bordered-dark alpha-table table-sm text-center">
                    <thead>
                        <strong>
                            <tr>
                                <th>NO. OF PCS.</th>
                                <th>PACKING</th>
                                <th>CONTENTS-DESCRIPTION(SAID TO CONTAIN)</th>
                                <th>DIM(inches) L*W*H</th>
                                <th>ACTUAL WEIGHT (LBS)</th>
                                <th>CHARGED WEIGHT (LBS)</th>
                            </tr>
                        </strong>
                    <tbody>
                        @foreach ($value['product_details'] as $key => $data)
                        <tr>
                            <td>{{ $data['no_of_pcs'] }}</td>
                            <td>{{ $data['packing'] }}</td>
                            <td>
                                @if (array_key_exists('item_description', $data))
                                {{ $data['item_description'] }}
                                @else
                                N/A
                                @endif
                            </td>
                            <td>{{ $data['dimension'] }}</td>
                            <td>{{ $data['actual_weight'] }}</td>
                            <td>{{ $data['charged_weight'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    </thead>
                </table>
                <table class="table table-bordered table-bordered-dark alpha-table table-sm ">
                    <tbody>
                        <tr>
                            <td>
                                <h6 class=" ml-2"><strong>TERMS AND CONDITIONS</strong></h6>
                                <p>
                                <table>
                                    <tbody>
                                        <tr>
                                            <p class="ml-2">SIGNATURE...........................</p>
                                        </tr>
                                    </tbody>
                                </table>
                                </p>
                            </td>
                            <td class="text-center">
                                <p><b> CONDITION & LIABILITY</b></p>
                                <p>I/We/hereby Agree to Terms & Condition of B2CShip (Pacific Impex LLC) and I / We
                                    Certify That the Nature of goods Are as Indicated on this Airway Bill</p>
                            </td>
                            <td>
                                <table>
                                    <tbody>
                                        <tr>
                                            <p class="ml-2">SIGNATURE................</p>
                                        </tr>
                                        <tr>
                                            <p class="ml-2">DATE......... TIME....... </p>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <!-- <hr class="dashed"> -->
                <!-- </div> -->
            </div>

        </div>
    </div>
</div>

@stop

@section('js')
<script>
    $(document).ready(function() {
        $('#Export_to_pdf').click(function(e) {

            e.preventDefault();
            var url = $(location).attr('href');
            var invoice_no = $('#pid').val();

            $.ajax({
                method: 'POST',
                url: "{{ route('export.shipntrack.invoice.pdf') }}",
                data: {
                    'url': url,
                    'invoice_no': invoice_no,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(response) {

                    window.location.href = '/shipntrack/invoice/dowload/pdf/' + invoice_no;
                    alert('Download pdf successfully');
                }
            });
        });
    });
</script>
@stop