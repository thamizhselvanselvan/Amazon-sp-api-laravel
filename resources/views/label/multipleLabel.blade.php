@extends('adminlte::page')
@section('title', 'Label')

@section('css')

    <link rel="stylesheet" href="/css/styles.css">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">

    <style type="text/css">
        body div strong {
            font-family: "Lato";
            font-weight: 900;
            font-size: 12px;
        }

        body * {
            font-family: "Lato";
            font-weight: 700;
            font-size: 14px;
        }

        .table_border th,
        .table_border td,
        .table_border tr,
        .table_border thead th,
        .return {
            border: 1px solid black;
        }

        .mb-1,
        .my-1 {
            margin-bottom: 0px !important;
        }

        .mt-1,
        .my-1 {
            margin-top: 0px !important;
        }

        .return,
        .prduct-details thead tr th {
            border-top: 0px !important;
        }

        @media print {
            @page {
                size: 4in 6.3in;
                margin: 0 !important;
                padding: 0 !important;

            }

            .table_border th,
            .table_border td,
            .table_border td,
            .table_border thead th,
            .return {
                border: 1px solid black !important;
            }

            .return,
            .prduct-details thead tr th {
                border-top: 0px !important;
            }

            .container-fluid {

                size: 4in 6in;
                width: 384px;
                height: 576px;
                margin: 0px;
                padding: 0px;
            }

            #label-container {
                margin: 0px;
                padding: 0px;
                /* padding-top: 5px; */
                transform-origin: 0 0;
                transform: scale(1.4);
            }

            #label-container .invoice {
                margin: 0px;
                padding: 0px;
                /*
                                                        width: 384px;
                                                        height: 576px;
                                                        */
            }
        }

        .ArToEn {
            font-size: 15px;
            line-height: 14px;
            margin: 0;
            margin-top: 5px;
        }

        .address {
            margin-top: 0;
            margin-bottom: 0;
            line-height: 15px;
            padding: 5px;
        }
    </style>
@stop
@section('content_header')
    <div class="label-company text-inverse f-w-600">
        <span class="pull-right hidden-print">
            <!-- <a href="javascript:void(0);" class="btn btn-sm btn-white m-b-10 p-l-5" id="Export_to_pdf"><i class="fa fa-file t-plus-1 text-danger fa-fw fa-lg"></i> Export as PDF</a> -->
            <a href="javascript:;" onclick="window.print()" class="btn btn-sm bg-info m-b-10 p-l-5"><i
                    class="fa fa-print t-plus-1 fa-fw fa-lg"></i> Print</a>

        </span>
        <br>
    </div>
    {{-- <div class="row">
    <div class="col">
        <a href="{{ route('label.search-label') }}" class="btn btn-primary btn-sm">
<i class="fas fa-long-arrow-alt-left"></i> Back
</a>
</div>
</div> --}}
@stop

@section('content')
    @foreach ($result as $key => $value)
        <div class="container label-container" id="label-container">
            <div class="col-md-12">
                <div class="label p-1">
                    <div class="label-content mb-0">
                        <!-- <div class="table-responsive"> -->
                        <table class="table table-label table-bordered table-bordered-dark pt-1 pb-0 mb-1 table_border">
                            <tbody>
                                <tr>
                                    <td class="pt-0 pb-0">
                                        <div class="row">
                                            <div class="col"></div>
                                            <div class="col p-0">
                                                <div class=" text-center mb-1">
                                                    @if ($value->forwarder)
                                                        {{ $value->forwarder }}
                                                    @else
                                                        &nbsp;
                                                    @endif

                                                </div>
                                                <img class="label-barcode-img"
                                                    src='data:image/png;base64,{!! $bar_code[$key] !!}' width="300px">
                                                <b>
                                                    <div class="text-center">
                                                        @if ($value->awb_no)
                                                            {{ $value->awb_no }}
                                                        @else
                                                            AWB is missing
                                                        @endif
                                                    </div>
                                                </b>
                                            </div>
                                            <div class="col">
                                                @if (isset($value->shipping_address['CountryCode']) && $value->shipping_address['CountryCode'] != 'AE')
                                                    <span>INTL</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="p-2">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class=" text-inverse m-b-5 text-left">
                                                    <strong> Invoice No: </strong>
                                                    {{ $value->order_no }}
                                                </div>
                                                <div class=" text-inverse m-b-5 text-left">
                                                    <strong> Store Name : </strong>
                                                    {{ $value->store_name }}
                                                </div>
                                                <div class=" text-inverse m-b-5 text-left">
                                                    <strong> Order Date: </strong>
                                                    {{ date('Y-m-d', strtotime($value->purchase_date)) }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="pt-1 pb-1">
                                        <div class="row">
                                            <div class="col">
                                                <div class="col">
                                                    <strong>Ship To: </strong>
                                                    <strong>
                                                        @if (isset($value->shipping_address['Name']))
                                                            {{ $value->shipping_address['Name'] }}
                                                        @endif
                                                    </strong><br>
                                                    <strong>Address: </strong>
                                                    @if (isset($value->shipping_address['AddressLine1']))
                                                        {{ $value->shipping_address['AddressLine1'] }},
                                                    @endif

                                                    @if (isset($value->shipping_address['AddressLine2']))
                                                        {{ $value->shipping_address['AddressLine2'] }}
                                                    @endif
                                                    <br>
                                                    <strong>City: </strong>
                                                    @if (isset($value->shipping_address['City']))
                                                        {{ $value->shipping_address['City'] }}
                                                    @else
                                                        NA
                                                    @endif
                                                    <br>
                                                    @if (isset($value->shipping_address['County']))
                                                        <strong>County: </strong>
                                                        {{ $value->shipping_address['County'] }}
                                                    @endif
                                                    <br>
                                                    @if (isset($value->shipping_address['country']))
                                                        <strong>Country: </strong>
                                                        {{ $value->shipping_address['country'] }}
                                                    @endif
                                                    <br>
                                                    @if (isset($value->shipping_address['Phone']))
                                                        <strong>Phone: </strong>
                                                        {{ $value->shipping_address['Phone'] }}
                                                    @endif
                                                    <br>
                                                    @if (isset($getTranslatedText[$key]['name']) ||
                                                            isset($getTranslatedText[$key]['addressline1']) ||
                                                            isset($getTranslatedText[$key]['addressline1']) ||
                                                            isset($getTranslatedText[$key]['city']) ||
                                                            isset($getTranslatedText[$key]['county']))
                                                        <p class="ArToEn"><strong>Delivery Address:</strong>
                                                            {{ $getTranslatedText[$key]['name'] == null ? $value->shipping_address['Name'] : $getTranslatedText[$key]['name'] }},
                                                            {{ $getTranslatedText[$key]['addressline1'] == null ? $value->shipping_address['AddressLine1'] ?? '' : $getTranslatedText[$key]['addressline1'] }},
                                                            {{ $getTranslatedText[$key]['addressline2'] == null ? $value->shipping_address['AddressLine2'] ?? '' : $getTranslatedText[$key]['addressline2'] }},
                                                            {{ $getTranslatedText[$key]['city'] == null ? $value->shipping_address['City'] : $getTranslatedText[$key]['city'] }},
                                                            {{ $getTranslatedText[$key]['county'] == null ? $value->shipping_address['County'] : $getTranslatedText[$key]['county'] }}
                                                    @endif

                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <table
                            class="table table-bordered table-bordered-dark product pt-0 mb-0 prduct-details table_border">
                            <thead>
                                <tr>
                                    <th class="text-left">Sr</th>
                                    <th class="text-center">Product Name</th>
                                    <th class="text-center">SKU</th>
                                    <th class="text-center">QTY</th>
                                    <!-- <th class="text-center">Price</th> -->
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($value->product as $key => $details)
                                    <tr class="mb-0">
                                        <td class="text-center p-1">{{ $key + 1 }}</td>
                                        <td class="text-center p-1">
                                            @php
                                                $new_word = wordwrap($details['title'], 20, "\n", true);
                                                echo "$new_word\n";
                                            @endphp
                                        </td>
                                        <td class="text-center p-1">{{ $details['sku'] }}</td>
                                        <td class="text-center p-1">{{ $details['qty'] }}</td>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class=" small return">
                            <p class="address">
                                <strong>Return Address:</strong>
                                Warehouse 61, Al Habtoor Warehouses, Industrial Area 3, Al Qusias, Dubai UAE
                            </p>
                        </div>
                        <!-- </div> -->
                    </div>
                </div>
            </div>
        </div>
        <p style="page-break-after: always;">&nbsp;</p>
    @endforeach
@stop

@section('js')
    <script>
        // $(document).ready(function() {
        //     window.print()
        // });
    </script>
@stop
