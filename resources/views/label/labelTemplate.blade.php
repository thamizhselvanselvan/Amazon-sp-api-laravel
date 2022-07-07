@extends('adminlte::page')
@section('title', 'Label')

@section('content_header')
<div class="label-company text-inverse f-w-600">
    <span class="pull-right hidden-print">
        <a href="javascript:void(0);" class="btn btn-sm btn-white m-b-10 p-l-5" id="Export_to_pdf"><i class="fa fa-file t-plus-1 text-danger fa-fw fa-lg"></i> Export as PDF</a>
        <a href="javascript:;" onclick="window.print()" class="btn btn-sm btn-white m-b-10 p-l-5"><i class="fa fa-print t-plus-1 fa-fw fa-lg"></i> Print</a>

    </span>
    <br>
</div>
@stop

@section('css')

<style type="text/css">
    @media print {

        @page {
            size: 4in 6in;
            margin: 0px;
            padding: 0px;
        }

        body {
            margin-top: 5px;
            margin-left: 0px;
            transform: scale(1.4);
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
            width: 384px;
            height: 576px;
            padding-top: 5px;
        }

        #label-container .label {
            margin: 0px;
            padding: 0px;
        }
    }
    
</style>
@stop

@section('content')
<input type="hidden" id="awb_no" value="{{$awb_no}}">

<div class="col-md-12" id="label-container">
    <div class="label p-1">
        <div class="label-content">
            <!-- <div class="table-responsive"> -->
            <table class="table table-label table-bordered table-bordered-dark<td pt-1 pb-0 mb-1">
                <tbody>
                    <tr>
                        <td class="pb-0">
                            <div class="row">
                                <div class="col p-0"></div>
                                <div class="col p-0">
                                    <img src='data:image/png;base64,{!! $bar_code !!}'>
                                    <b>
                                        <div class="text-center">{{ $result->awb_no }}</div>
                                    </b>
                                </div>
                                <div class="col p-0"></div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="pt-1 pb-1">
                            <div class="row ">
                                <div class="col p-0">
                                    <div class="text-inverse m-b-5 text-left"><strong>
                                            Invoice No: </strong> {{$result->order_no}}
                                    </div>
                                    <div class="text-inverse m-b-5 text-left"><strong>
                                            Order Date: </strong>{{date('Y-m-d', strtotime($result->purchase_date))}}
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
                                    <strong>{{$result->shipping_address['Name']}}</strong><br>
                                    
                                    <strong>Address: </strong>
                                    @if(isset($result->shipping_address['AddressLine1']))
                                    {{$result->shipping_address['AddressLine1']}},
                                    @endif

                                     @if(isset($result->shipping_address['AddressLine2']))
                                    {{$result->shipping_address['AddressLine2']}}
                                    @endif
                                    <br>
                                    <strong>City: </strong>
                                    @if(isset($result->shipping_address['City']))
                                    {{$result->shipping_address['City']}}
                                    @else
                                    NA
                                    @endif
                                    <br>
                                    @if(isset($result->shipping_address['County']))
                                    <strong>County: </strong>
                                    {{$result->shipping_address['County']}}
                                    @endif
                                    <br>
                                    @if(isset($result->shipping_address['country']))
                                    <strong>Country: </strong>
                                    {{$result->shipping_address['country']}}
                                    @endif
                                    <br>
                                    @if(isset($result->shipping_address['Phone']))
                                    <strong>Phone: </strong>
                                    {{$result->shipping_address['Phone']}}
                                    @endif
                                    <br>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <table class="table table-bordered table-bordered-dark product pt-0">
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
                    @foreach ($result->product as $key => $value)
                    <tr>
                        <td class="text-center p-1">{{$key+1}}</td>
                        <td class="p-1">{{$value['title']}}</td>
                        <td class="text-center p-1">{{$value['sku']}}</td>
                        <td class="text-center p-1">{{$value['qty']}}</td>
                        <td class="text-center p-1">{{$value['order_total']->CurrencyCode}} {{$value['order_total']->Amount}}</td>
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

    @stop

    @section('js')
    <script>
        $(document).ready(function() {
            $('#Export_to_pdf').click(function(e) {
                e.preventDefault();
                var url = $(location).attr('href');
                var awb_no = $('#awb_no').val();
                // alert(url);
                $.ajax({
                    method: 'POST',
                    url: "{{ url('/label/export-pdf')}}",
                    data: {
                        'url': url,
                        'awb_no': awb_no,
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(response) {

                        window.location.href = '/label/download/' + awb_no;
                        alert('Download pdf successfully');
                    }
                });
            });
        });
    </script>
    @stop