@extends('adminlte::page')

@section('title', 'Invoice')

@section('css')
    <link rel="stylesheet" href="/css/styles.css">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">
@stop
@section('content_header')
<div class="invoice-company text-inverse f-w-600">
      <span class="pull-right hidden-print">
        <a href="javascript:;" onclick="window.print()" class="btn btn-sm btn-white m-b-10 p-l-5 bg-primary"><i class="fa fa-print t-plus-1 fa-fw fa-lg"></i> Print</a> 
      </span>
      <br>
   </div>        
@stop

@section('content')
    @foreach ($data as $record)

        @foreach ($record as $value)
            <div class="container">
                <div class="col-md-12">
                    <div class="invoice">
                        <!-- begin invoice-company -->
                        <div class="container">
                            <h4 class="text-center"> <strong> TAX INVOICE </strong> </h4>
                        </div>   
                        
                        <div class="col-md-12 invoice-date text-left" >
                        
                            <!-- <small>Invoice / July period</small> -->
                            <div class=" text-inverse m-t-5"><strong> INVOICE DATE: </strong> {{ $value->invoice_date }}</div>
                            <div class=" text-inverse m-t-5"><strong> INVOICE NO: </strong> {{ $value->invoice_no }}</div>
                            <div class=" text-inverse m-t-5"><strong> CHANNEL: </strong> {{ $value->channel }}</div>
                            <div class=" text-inverse m-t-5"><strong> SHIPPED BY: </strong> {{ $value->shipped_by }}</div>
                            <div class=" text-inverse m-t-5"><strong> AWB NO: </strong> {{ $value->awb_no }}</div>
                            <div class=" text-inverse m-t-5"><strong> HSN CODE: </strong> {{ $value->hsn_code }}</div>
                            <div class=" text-inverse m-t-5"><strong> ARN No: </strong> {{ $value->arn_no }}</div>
                           
                        </div>
                        <div class="col-md-12 invoice-date text-right" >
                            <div class=" text-inverse m-t-5">
                                <div class="row">
                                    <div class="col"></div>
                                    <div class="col"></div>
                                    <div class="col">{!! $invoice_bar_code !!}</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="invoice-header">
                            <div class="invoice-from">
                            <address class="m-t-5 m-b-5">
                                <strong class="text-inverse"><h6><b>COMPANY NAME</b></h6></strong><hr>
                                <b> {{ $value->store_name }} </b><br>
                                {{ $value->store__add }}
                            </address>
                            </div>
                            
                            <div class="invoice-to">
                            
                            <address class="m-t-5 m-b-5">
                                <strong class="text-inverse"><h6><b>BILL TO </b></h6></strong><hr>
                                <b> {{ $value->bill_to_name }} </b><br>
                                {{ $value->bill_to_add }}
                            </address>
                            </div>
                            <div class="invoice-to">
                            
                            <address class="m-t-5 m-b-5">
                                <strong class="text-inverse"><h6><b>SHIP TO </b></h6></strong><hr>
                                <b> {{ $value->bill_to_name }} </b><br>
                                {{ $value->ship_to_add }}
                            </address>
                            </div>
                        </div>
                        
                        <!-- end invoice-header -->
                        <!-- begin invoice-content -->
                        <div class="invoice-content">
                            <!-- begin table-responsive -->
                            <div class="table-responsive">
                            <table class="table table-invoice table-bordered table-bordered-dark">
                                <thead>
                                    <tr>
                                        <th class="text-center">SR. NO.</th>
                                        <th class="text-center">ITEM DESCRIPTION</th>
                                        <th class="text-center">HSN CODE</th>
                                        <th class="text-center" width="10%">QTY</th>
                                        <th class="text-center" width="10%">PRODUCT PRICE</th>
                                        <th class="text-center" width="10%">TAXABLE VALUE</th>
                                        <th class="text-right" width="20%">TOTAL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-center"> {{ $value->sr_no }} </td>
                                        <td class="text-center"> {{ $value->item_description }} </td>
                                        
                                        @if ( $value->hsn_code == '')
                                        <td class="text-center">{{ $value->hsn_code }}</td>
                                        @else
                                        <td class="text-center">{{ $value->hsn_code }}</td>
                                        @endif

                                        @if ( $value->qty =='')
                                        <td class="text-center">{{ 0 }}</td>
                                        @else
                                        <td class="text-center">{{ $value->qty }}</td>
                                        @endif
                                        
                                        @if ( $value->product_price =='')
                                        <td class="text-center">{{ 0 }}</td>
                                        @else
                                        <td class="text-center">{{$value->currency}} {{ $value->product_price}}</td>
                                        @endif

                                        @if ( $value->taxable_value =='')
                                        <td class="text-center">{{ 0 }}</td>
                                        @else
                                        <td class="text-center">{{ $value->taxable_value}}</td>
                                        @endif
                                        
                                        @if( $value->grand_total =='' )
                                        <td class="text-right">{{ 0 }}</td>
                                        @else
                                        <td class="text-right">{{$value->currency}} {{ $value->grand_total }}</td>
                                        @endif
                                    
                                    </tr>
                                    
                                </tbody>
                            </table>
                            </div>
                            <!-- end table-responsive -->
                            <!-- begin invoice-price -->
                            <div class="invoice-price">
                            <div class="invoice-price-left">
                                
                            </div>
                            <div class="invoice-price-right">
                                @if ($value->grand_total == '')
                                <small>GRAND TOTAL</small> <span class="f-w-600">{{ 0 }}</span>
                                @else
                                <small>GRAND TOTAL</small> <span class="f-w-600">{{$value->currency}} {{ $value->grand_total }}</span>
                                @endif
                                
                            </div>
                            </div>
                            <!-- end invoice-price -->
                            <p class=" mb-0 text-center">This is system generated invoice, it may contain only digital signature</p>
                            <!-- <div class="container"> -->
                            <div class="row" class="border-dashed">
                                <div class="col "><img src="{{URL::asset('/image/bombinoImage.jpg')}}" class="img-fluid rounded mx-auto d-block" alt="Bombino_express"></div>
                                <div class="col border">
                                    <!-- <div class="table-responsive"> -->
                                        <table class="table-sm text-center table-size">
                                        <thead>
                                            <tr>
                                                <th><strong> CONSIGNMENT NOTE NUMBER </strong></th>
                                            </tr>
                                            <tr>
                                                <th> {!!$bar_code !!} </th>
                                            </tr>
                                            <tr>
                                                <th>{{$value->invoice_no}}</th>
                                            </tr>
                                        </thead>
                                        </table>
                                    <!-- </div> -->
                                </div>
                                <div class="col">
                                    <!-- <div class="row"><p class="text-center">SERVICE</p></div><hr>
                                    <div class="row"><p class="text-center">SELF</p></div><hr> -->
                                    <div class="table-responsive">
                                        <table class="table table-invoice table-bordered table-bordered-dark table-sm text-center">
                                        <thead>
                                            <tr><td colspan="2" class="text-center">SERVICE</td></tr>
                                            <tr><td colspan="2" class="text-center">SELF</td></tr>
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
                            </div>
                            <br>
                            <table class="table table-invoice table-bordered table-bordered-dark table-sm text-left">
                                <thead>
                                    <tr>
                                        <th>SHIPPER A/C: </th>
                                        <th>DATE:</th>     
                                        <th >ONFD NO:</th>
                                        
                                    </tr>
                                </thead>
                            </table>
                            <table class="table table-bordered border">
                                <tbody>
                                    <div class="row">
                                        <div class="col ">
                                            <b class="bg-dark text-white mt-4 ml-2"> CONSIGNOR</b><hr>
                                            <p class=" ml-2 mt-2"><b> {{ $value->bill_to_name }} </b><br> {{ $value->bill_to_add }} </p>
                                        </div>
                                        <div class="col ">
                                            <b class="bg-dark text-white mt-4 ml-2"> CONSIGNEE </b><hr>
                                            <p class=" ml-2 mt-2"><b> {{ $value->bill_to_name }} </b><br> {{ $value->ship_to_add }} </p>
                                        </div>
                                    </div>
                                </tbody>
                            </table>
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
                                        <tr>
                                        <td>1</td>
                                        <td>SPX</td>
                                        <td>{{ $value->item_description }}</td>
                                        <td>7*3*3</td>
                                        <td>1.000</td>
                                        <td>1.000</td>
                                        </tr>
                                    </tbody>
                                </thead>
                            </table>
                            <table class="table table-bordered table-bordered-dark alpha-table table-sm ">
                                <tbody>
                                    <tr>
                                        <td><h6 class=" ml-2"><strong>TERMS AND CONDITIONS</strong></h6>
                                        <p><table><tbody><tr><p class="ml-2">SIGNATURE...............................</p></tr></tbody></table></p></td>
                                        <td class="text-center"><p><b> CONDITION & LIABILITY</b></p>
                                        <p> I/We/ hereby Agree to Terms & Condition of B2CShip (Pacific Impex LLC) and I / We Certify That the Nature of goods Are as Indicated on this Airway Bill</p>
                                        </td>
                                        <td>
                                        <table><tbody><tr><p class="ml-2">SIGNATURE..................................</p></tr><tr><p class="ml-2">DATE:................ TIME:.............. </p></tr></tbody></table>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <hr class="dashed">
                        </div>
                    </div>
                </div>
            </div>
            <p style="page-break-after: always;">&nbsp;</p>
        @endforeach
    @endforeach
@stop