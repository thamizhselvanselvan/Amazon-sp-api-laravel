@extends('adminlte::page')

@section('title', 'Invoice')

@section('css')

    <link rel="stylesheet" href="/css/styles.css">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">

    <style>
        .dashed {
            border: 1px dashed black;
        }
    </style>
@stop

@section('content_header')
    <div class="invoice-company text-inverse f-w-600">
        <span class="pull-right hidden-print">
            {{-- <a href="javascript:void(0);" class="btn btn-sm btn-white m-b-10 p-l-5" id="Export_to_pdf"><i
                    class="fa fa-file t-plus-1 text-danger fa-fw fa-lg"></i> Export as PDF</a> --}}
            <a href="javascript:;" onclick="window.print()" class="btn btn-sm btn-white m-b-10 p-l-5"><i
                    class="fa fa-print t-plus-1 fa-fw fa-lg"></i> Print</a>
        </span>
        <br>
    </div>
@stop

@section('content')

    <div class="container">
        <div class="col-md-12">
            <div class="invoice">
                <div class="container">
                    <h4 class="text-center mt-0"> <strong> TAX INVOICE </strong> </h4>
                </div>

                <div class="col-md-12 invoice-date text-left">

                    <div class=" text-inverse m-t-5"><strong> INVOICE DATE: </strong> {{ $value['invoice_date'] }}</div>
                    <div class=" text-inverse m-t-5"><strong> INVOICE NO: </strong> {{ $value['invoice_no'] }}</div>
                    <div class=" text-inverse m-t-5"><strong> CHANNEL: </strong> {{ $value['channel'] }}</div>
                    <div class=" text-inverse m-t-5"><strong> SHIPPED BY: </strong> {{ $value['shipped_by'] }}</div>
                    <div class=" text-inverse m-t-5"><strong> AWB NO.: </strong> {{ $value['awb_no'] }}</div>

                    <div class=" text-inverse m-t-5"><strong> ARN NO.: </strong> {{ $value['arn_no'] }}</div><br>
                    <div class="invoice-detail">
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

                <div class="invoice-content">
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
                                                NA
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
                                            @if ($data['quantity'] == '')
                                                {{ 0 }}
                                            @else
                                                {{ $data['quantity'] }}
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
                    <div class="invoice-price">
                        <div class="invoice-price-left">

                        </div>
                        <div class="invoice-price-right">
                            @if ($data['currency'] == '')
                                <small>GRAND TOTAL</small> <span class="f-w-600">{{ 0 }}</span>
                            @else
                                <small><strong> GRAND TOTAL </strong> </small> <span
                                    class="f-w-600">{{ $data['currency'] }} {{ $data['grand_total'] }}</span>
                            @endif

                        </div>
                    </div>
                    <p class=" mb-0 text-center">This is system generated invoice, it may contain only digital signature.
                    </p>

                </div>
            </div>
        </div>
    </div>

@stop

@section('js')
    <script></script>
@stop
