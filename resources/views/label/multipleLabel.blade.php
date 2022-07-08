@extends('adminlte::page')
@section('title', 'Label')

@section('css')

<link rel="stylesheet" href="/css/styles.css">
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">

<style type="text/css">
    @media print {
        @page {
            size: 4in 6in;
            margin: 0 !important;
            padding: 0 !important;
        }
        .container-fluid {
            /*
            size: 4in 6in;
            width: 384px;
            height: 576px;
            */
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
</style>
@stop
@section('content_header')
<div class="label-company text-inverse f-w-600">
    <span class="pull-right hidden-print">
        <!-- <a href="javascript:void(0);" class="btn btn-sm btn-white m-b-10 p-l-5" id="Export_to_pdf"><i class="fa fa-file t-plus-1 text-danger fa-fw fa-lg"></i> Export as PDF</a> -->
        <a href="javascript:;" onclick="window.print()" class="btn btn-sm bg-info m-b-10 p-l-5"><i class="fa fa-print t-plus-1 fa-fw fa-lg"></i> Print</a>

    </span>
    <br>
</div>
@stop

@section('content')
@foreach ($result as $key => $value)
<div class="container label-container" id="label-container">
    <div class="col-md-12">
        <div class="label p-1">
            <div class="label-content mb-0">
                <!-- <div class="table-responsive"> -->
                <table class="table table-label table-bordered table-bordered-dark ">
                    <tbody>
                        <tr>
                            <td class="pb-0 pt-2">
                                <div class="row">
                                    <div class="col"></div>
                                    <div class="col text-center">{!! $bar_code[$key] !!} <b> {{ $value->awb_no }} </b></div>
                                    <div class="col"></div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="p-2">
                                <div class="row">
                                    <div class="col-12">
                                        <div class=" text-inverse m-b-5 text-left">
                                            <strong> Invoice No: </strong>
                                            {{$value->order_no}}
                                        </div>
                                        <div class=" text-inverse m-b-5 text-left">
                                            <strong> Order Date: </strong>
                                            {{date('Y-m-d', strtotime($value->purchase_date))}}
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
                                            <strong>{{$value->shipping_address['Name']}}</strong><br>
                                            @if(isset($value->shipping_address['AddressLine1']))
                                            {{$value->shipping_address['AddressLine1']}},
                                            @endif

                                            @if(isset($value->shipping_address['AddressLine2']))
                                            {{$value->shipping_address['AddressLine2']}}
                                            @endif
                                            <br>
                                            <strong>City: </strong>
                                            @if(isset($value->shipping_address['City']))
                                            {{$value->shipping_address['City']}}
                                            @else
                                            NA
                                            @endif
                                            <br>
                                            @if(isset($value->shipping_address['County']))
                                            <strong>County: </strong>
                                            {{$value->shipping_address['County']}}
                                            @endif
                                            <br>
                                            @if(isset($value->shipping_address['country']))
                                            <strong>Country: </strong>
                                            {{$value->shipping_address['country']}}
                                            @endif
                                            <br>
                                            @if(isset($value->shipping_address['Phone']))
                                            <strong>Phone: </strong>
                                            {{$value->shipping_address['Phone']}}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table class="table table-bordered table-bordered-dark product">
                    <thead>
                        <tr>
                            <th class="text-left">Sr</th>
                            <th class="text-center">Product Name</th>
                            <th class="text-center">SKU</th>
                            <th class="text-center">QTY</th>
                            <th class="text-center">Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($value->product as $key => $details)
                        <tr class="mb-0">
                            <td class="text-center p-1">{{$key+1}}</td>
                            <td class="text-center p-1">{{$details['title']}}</td>
                            <td class="text-center p-1">{{$details['sku']}}</td>
                            <td class="text-center p-1">{{$details['qty']}}</td>
                            <td class="text-center p-1">{{$details['order_total']->CurrencyCode}} {{$details['order_total']->Amount}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-1 p-1 small return">
                    <div>Return Address:</div>
                    <span>Mahzuz, Al Habtoor Warehouse No.27, Al QusaisIndustrial Area 3 Mumbai, MH, IN, 400025</span>
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