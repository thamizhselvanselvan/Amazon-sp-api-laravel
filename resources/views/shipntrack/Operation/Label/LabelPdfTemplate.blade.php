@extends('adminlte::page')
@section('title', 'ShipnTrack Label')

@section('content_header')
    <div class="label-company text-inverse f-w-600">
        <span class="pull-right hidden-print">

            <a href="javascript:;" onclick="window.print()" class="btn btn-sm btn-white m-b-10 p-l-5"><i
                    class="fa fa-print t-plus-1 fa-fw fa-lg"></i> Print</a>

        </span>
        <br>
    </div>
@stop

@section('css')

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
        .table_border td,
        .table_border thead th,
        .return {
            border: 1px solid black !important;
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
                size: 4in 6in;
                margin: 0px;
                padding: 0px;
            }

            .table_border th,
            .table_border td,
            .table_border tr,
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

            #label-container .label {
                margin: 0px;
                padding: 0px;
            }
        }

        .address {
            margin-top: 0;
            margin-bottom: 0;
            line-height: 15px;
            padding: 5px;
        }
    </style>
@stop

@section('content')

    <div class="col-md-12" id="label-container">
        <div class="label p-1">
            <div class="label-content">

                <table class="table table-label table-bordered table-bordered-dark<td pt-1 pb-0 mb-1 table_border">
                    <tbody>
                        <tr>
                            <td class="pt-0 pb-0">
                                <div class="row text-center">

                                    <div class="col p-0 ">
                                        <div class="text-center">
                                            @if ($records[0]['forwarder'] == '' || $records[0]['forwarder'] == null)
                                                &nbsp;
                                            @else
                                                {{ $records[0]['forwarder'] }}
                                            @endif

                                        </div>
                                        <img class="label-barcode-img" src='data:image/png;base64,{!! $bar_code !!}'>
                                        <b>
                                            <div class="text-center">
                                                @if ($records[0]['awb_no'] == '' || $records[0]['awb_no'] == null)
                                                    AWB is missing
                                                @else
                                                    {{ $records[0]['awb_no'] }}
                                                @endif
                                            </div>
                                        </b>
                                    </div>

                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="pt-1 pb-1">
                                <div class="row ">
                                    <div class="col p-0">
                                        <div class="text-inverse m-b-5 text-left"><strong>
                                                Invoice No: </strong> {{ $records[0]['order_no'] }}
                                        </div>
                                        <div class="text-inverse m-b-5 text-left"><strong>
                                                Order Date:
                                            </strong>{{ date('Y-m-d', strtotime($records[0]['order_date'])) }}
                                        </div>
                                    </div>
                                </div>

                            </td>
                        </tr>
                        <tr>
                            <td class="pt-1 pb-1">
                                <div class="row">
                                    <div class="col p-0">
                                        <strong>Ship To: </strong>
                                        <strong>
                                            @if (isset($records[0]['customer_name']))
                                                {{ $records[0]['customer_name'] }}
                                            @endif
                                        </strong><br>

                                        <strong>Address: </strong>
                                        @if (isset($records[0]['address']))
                                            {{ $records[0]['address'] }},
                                        @endif

                                        <br>
                                        <strong>City: </strong>
                                        @if (isset($records[0]['city']))
                                            {{ $records[0]['city'] }}
                                        @else
                                            NA
                                        @endif
                                        <br>
                                        @if (isset($records[0]['county']))
                                            <strong>County: </strong>
                                            {{ $records[0]['county'] }}
                                        @endif
                                        <br>
                                        @if (isset($records[0]['country']))
                                            <strong>Country: </strong>
                                            {{ $records[0]['country'] }}
                                        @endif
                                        <br>
                                        @if (isset($records[0]['phone']))
                                            <strong>Phone: </strong>
                                            {{ $records[0]['phone'] }}
                                        @endif

                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table class="table table-bordered table-bordered-dark product pt-0 mb-0 prduct-details table_border">
                    <thead>
                        <tr>
                            <th class="text-left">Sr</th>
                            <th class="text-center">Product Name</th>
                            <th class="text-center">SKU</th>
                            <th class="text-center">QTY</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($records[0]['product_name'] as $key => $value)
                            <tr>
                                <td class="text-center p-1">{{ $key + 1 }}</td>
                                <td class="p-1">
                                    @php
                                        $new_word = wordwrap($value, 20, "\n", true);
                                        echo "$new_word\n";
                                    @endphp
                                </td>
                                <td class="text-center p-1">{{ $records[0]['sku'][$key] ?? '' }}</td>
                                <td class="text-center p-1">{{ $records[0]['quantity'][$key] ?? '' }}</td>

                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>

                <div class=" small return">

                    <p class="address"><strong>Return Address:</strong>
                        Warehouse 61, Al Habtoor Warehouses, Industrial Area 3, Al Qusias, Dubai UAE
                    </p>

                </div>
            </div>

        </div>

    @stop
