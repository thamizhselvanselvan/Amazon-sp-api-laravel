@extends('adminlte::page')
@section('title', 'Label')

@section('css')

<link rel="stylesheet" href="/css/styles.css">
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">
@stop
@section('content_header')
<div class="invoice-company text-inverse f-w-600">
    <span class="pull-right hidden-print">
        <!-- <a href="javascript:void(0);" class="btn btn-sm btn-white m-b-10 p-l-5" id="Export_to_pdf"><i class="fa fa-file t-plus-1 text-danger fa-fw fa-lg"></i> Export as PDF</a> -->
        <a href="javascript:;" onclick="window.print()" class="btn btn-sm bg-info m-b-10 p-l-5"><i class="fa fa-print t-plus-1 fa-fw fa-lg"></i> Print</a>

    </span>
    <br>
</div>
@stop

@section('content')
@foreach ($result as $key => $value)
<div class="container label-container">
    <div class="col-md-12">
        <div class="invoice">
            <div class="invoice-content">
                <!-- <div class="table-responsive"> -->
                <table class="table table-invoice table-bordered table-bordered-dark">
                    <tbody>
                        <tr>
                            <td>
                                <div class="row p-2">
                                    <div class="col"></div>
                                    <div class="col">{!! $bar_code[$key] !!} <b> {{ $value->awb_no }} </b></div>
                                    <div class="col"></div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="row">
                                    <div class="col-6">
                                        <h5><strong> Tracking Details: {{ $value->awb_no }} </strong></h5>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <h5><strong> Prepaid : </strong></h5>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6><b>SHIP TO </b></h6>
                                        @foreach ($value->shipping_address as $key => $address )
                                        {{$address}},
                                        @endforeach
                                        <br>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class=" text-inverse m-b-5 text-left"><strong> Invoice No: </strong> {{$value->order_no}} </div>
                                        <div class=" text-inverse m-b-5 text-left"><strong> Order Date: </strong> {{date('Y-m-d', strtotime($value->purchase_date))}} </div>
                                        <div class=" text-inverse m-b-5 text-left"><strong> INVOICE DATE: </strong> {{date('Y-m-d', strtotime($value->purchase_date))}}</div>
                                        <div class=" text-inverse m-b-5 text-left"><strong> Pieces: </strong> {{$value->order_item}} </div>
                                        <div class=" text-inverse m-b-5 text-left"><strong> Order ID: </strong> {{$value->order_no}} </div>
                                        <div class=" text-inverse m-b-5 text-left"><strong> Weight: </strong> {{$value->package_dimensions['Weight']->value}} {{$value->package_dimensions['Weight']->Units}}</div>
                                        <div class=" text-inverse m-b-5 text-left"><strong> Dimensions: </strong> {{$value->package_dimensions['Height']->value}} X {{$value->package_dimensions['Length']->value}} X
                                            {{$value->package_dimensions['Width']->value}}
                                            {{$value->package_dimensions['Length']->Units}}
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table class="table table-bordered table-bordered-dark">
                    <thead>
                        <tr>
                            <th class="text-left">SR. NO.</th>
                            <th class="text-center">Product Name</th>
                            <th class="text-center">SKU</th>
                            <th class="text-center" width="10%">QTY</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($value->product as $key => $details)
                        <tr>
                            <td class="text-center">{{$key+1}}</td>
                            <td class="text-center">{{$details['title']}}</td>
                            <td class="text-center">{{$details['sku']}}</td>
                            <td class="text-center">{{$details['qty']}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <!-- </div> -->
            </div>
        </div>
    </div>
</div>
<p style="page-break-after: always;">&nbsp;</p>
@endforeach
@stop

@section('js')
@stop