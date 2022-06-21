@extends('adminlte::page')
@section('title', 'Label')

@section('css')

<link rel="stylesheet" href="/css/styles.css">
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">
@stop
@section('content_header')
<div class="invoice-company text-inverse f-w-600">
    <span class="pull-right hidden-print">
        <a href="javascript:void(0);" class="btn btn-sm btn-white m-b-10 p-l-5" id="Export_to_pdf"><i class="fa fa-file t-plus-1 text-danger fa-fw fa-lg"></i> Export as PDF</a>
        <a href="javascript:;" onclick="window.print()" class="btn btn-sm btn-white m-b-10 p-l-5"><i class="fa fa-print t-plus-1 fa-fw fa-lg"></i> Print</a>

    </span>
    <br>
</div>
@stop

@section('content')
<input type="hidden" id="awb_no" value="{{$awb_no}}">

<div class="container label-container">
    <div class="col-md-12">
        <div class="invoice">
            <div class="invoice-content">
                <div class="table-responsive">
                    <table class="table table-invoice table-bordered table-bordered-dark">
                        <tbody>
                            <tr>
                                <td>
                                    <div class="row">
                                        <div class="col "></div>
                                        <div class="col ">{!! $bar_code !!} <b> {{ $result->awb_no }} </b></div>
                                        <div class="col "></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h4><strong> Tracking Details : </strong></h4>
                                        </div>
                                        <div class="col-md-6">
                                            <h4 class="text-center"><strong>{{ $result->awb_no }}</strong></h4>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <h4><strong> PREPAID : </strong></h4>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6><b>SHIP TO </b><br>
                                                @foreach ($result->shipping_address as $key => $value )
                                                    {{$value}},
                                                @endforeach
                                            </h6>
                                            <br>
                                        </div>
                                        <div class="col-md-6">
                                            <div class=" text-inverse m-b-5 text-left"><strong>
                                                    Order Date: </strong>{{$result->purchase_date}} </div>
                                            <div class=" text-inverse m-b-5 text-left"><strong>
                                                    Invoice No: </strong> {{$result->order_no}} </div>
                                            <div class=" text-inverse m-b-5 text-left"><strong>
                                                    Invoice Date: </strong> {{$result->purchase_date}} </div>
                                            <div class=" text-inverse m-b-5 text-left"><strong>
                                                    Pieces: </strong> {{$result->order_item}} </div>
                                            <div class=" text-inverse m-b-5 text-left"><strong>
                                                    Order ID: </strong> {{$result->order_no}} </div>
                                            <div class=" text-inverse m-b-5 text-left"><strong> Weight: </strong> {{$result->package_dimensions['Weight']->value}} {{$result->package_dimensions['Weight']->Units}}</div>
                                            <div class=" text-inverse m-b-5 text-left"><strong> Dimensions: {{$result->package_dimensions['Height']->value}} X {{$result->package_dimensions['Length']->value}} X 
                                            {{$result->package_dimensions['Width']->value}} 
                                            {{$result->package_dimensions['Length']->Units}}
                                            </strong> </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="invoice-content">
                <div class="table-responsive">
                    <table class="table table-invoice table-bordered table-bordered-dark">
                        <thead>
                            <tr>
                                <th class="text-left">S/N</th>
                                <th class="text-center">Product Name</th>
                                <th class="text-center">SKU</th>
                                <th class="text-center" width="10%">QTY</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($result->product as $key => $value)
                            <tr>
                                <td class="text-center">{{$key+1}}</td>
                                <td class="">{{$value['title']}}</td>
                                <td class="text-center">{{$value['sku']}}</td>
                                <td class="text-center">{{$value['qty']}}</td>
                            </tr>
                            @endforeach
                            <tr>
                                <!-- <td colspan="4"><strong>Return Address :</strong> Mahzuz, Al Habtoor Warehouse No.27 ,Al QusaisIndustrial Area 3 mumbai, MH, IN, 400025</td> -->
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
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