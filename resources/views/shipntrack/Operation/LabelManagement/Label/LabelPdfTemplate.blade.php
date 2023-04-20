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
                transform-origin: 0 0;
                transform: scale(1.4);
            }

            #label-container .invoice {
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
    @foreach ($records as $key => $record)
        <div class="container label-container" id="label-container">
            <div class="col-md-12">
                <div class="label p-1">
                    <div class="label-content">

                        <table class="table table-label table-bordered table-bordered-dark<td pt-1 pb-0 mb-1 table_border">
                            <tbody>
                                <tr>
                                    <td class="pt-0 pb-0">
                                        <div class="row text-center">

                                            <div class="col p-0 ">
                                                <div class="text-center">
                                                    @if ($record['forwarder'] == '' || $record['forwarder'] == null)
                                                        &nbsp;
                                                    @else
                                                        {{ $record['forwarder'] }}
                                                    @endif

                                                </div>
                                                <img class="label-barcode-img"
                                                    src='data:image/png;base64,{!! $bar_code[$key] !!}'>
                                                <b>
                                                    <div class="text-center">
                                                        @if ($record['awb_no'] == '' || $record['awb_no'] == null)
                                                            AWB is missing
                                                        @else
                                                            {{ $record['awb_no'] }}
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
                                                        Invoice No: </strong> {{ $record['order_no'] }}
                                                </div>
                                                <div class="text-inverse m-b-5 text-left"><strong>
                                                        Order Date:
                                                    </strong>{{ date('Y-m-d', strtotime($record['order_date'])) }}
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
                                                    @if (isset($record['customer_name']))
                                                        {{ $record['customer_name'] }}
                                                    @endif
                                                </strong><br>

                                                <strong>Address: </strong>
                                                @if (isset($record['address']))
                                                    {{ $record['address'] }},
                                                @endif

                                                <br>
                                                <strong>City: </strong>
                                                @if (isset($record['city']))
                                                    {{ $record['city'] }}
                                                @else
                                                    NA
                                                @endif
                                                <br>
                                                @if (isset($record['county']))
                                                    <strong>County: </strong>
                                                    {{ $record['county'] }}
                                                @endif
                                                <br>
                                                @if (isset($record['country']))
                                                    <strong>Country: </strong>
                                                    {{ $record['country'] }}
                                                @endif
                                                <br>
                                                @if (isset($record['phone']))
                                                    <strong>Phone: </strong>
                                                    {{ $record['phone'] }}
                                                @endif

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
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($record['product_name'] as $key => $value)
                                    <tr>
                                        <td class="text-center p-1">{{ $key + 1 }}</td>
                                        <td class="p-1">
                                            @php
                                                $new_word = wordwrap($value, 20, "\n", true);
                                                echo "$new_word\n";
                                            @endphp
                                        </td>
                                        <td class="text-center p-1">{{ $record['sku'][$key] ?? '' }}</td>
                                        <td class="text-center p-1">{{ $record['quantity'][$key] ?? '' }}</td>

                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>

                        </table>

                        <div class=" small return">

                            <p class="address"><strong>Return Address:</strong>
                                {{ $record['return_address'] }}
                            </p>

                        </div>
                    </div>

                </div>
                <p style="page-break-after: always;">&nbsp;</p>
            </div>
        </div>
    @endforeach
@stop
