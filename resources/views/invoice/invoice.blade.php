@extends('adminlte::page')

@section('title', 'Invoice')

@section('css')

    <link rel="stylesheet" href="/css/styles.css">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">
@stop
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
  
<input type="hidden" id="pid" value="{{$invoice_no}}" >
<div class="container">
   <div class="col-md-12">
      <div class="invoice">
         <!-- begin invoice-company -->
         <div class="container">
            <h4 class="text-center">TAX INVOICE</h4>
         </div>   
           
            <div class="col-md-12 invoice-date text-left" >
               @foreach ($data as $key =>$value)
                  
               
               <!-- <small>Invoice / July period</small> -->
               <div class="date text-inverse m-t-5">INVOICE DATE: {{ $value->invoice_date }}</div>
               <div class="date text-inverse m-t-5">INVOICE NO: {{ $value->invoice_no }}</div>
               <div class="date text-inverse m-t-5">ORDER NO: {{ $value->order_no }}</div>
               <div class="date text-inverse m-t-5">ORDER DATE: {{ $value->order_date }}</div>
               <div class="date text-inverse m-t-5">CHANNEL: {{ $value->channel }}</div>
               <div class="date text-inverse m-t-5">SHIPPED BY: {{ $value->shipped_by }}</div>
               <div class="date text-inverse m-t-5">AWB NO: {{ $value->awb_no }}</div>
               
               <div class="invoice-detail">
                  <br>
                  <!-- Services Product -->
               </div>
               @endforeach
            </div>
         
         <!-- end invoice-company -->
         <!-- begin invoice-header -->
         @foreach ($data as $key => $value )
             
         
         <div class="invoice-header">
            <div class="invoice-from">
               <address class="m-t-5 m-b-5">
                  <strong class="text-inverse">STORE</strong><hr><br>
                  
                  {{ $value->store_name_add }}
               </address>
            </div>
            
            <div class="invoice-to">
               
               <address class="m-t-5 m-b-5">
                  <strong class="text-inverse">BILL TO</strong><hr><br>
                  
                  {{ $value->ship_to_add }}
               </address>
            </div>
            <div class="invoice-to">
               
               <address class="m-t-5 m-b-5">
                  <strong class="text-inverse">SHIP TO</strong><hr><br>
                  
                  {{ $value->ship_to_add }}
               </address>
            </div>
         </div>
         
         <!-- end invoice-header -->
         <!-- begin invoice-content -->
         <div class="invoice-content">
            <!-- begin table-responsive -->
            <div class="table-responsive">
               <table class="table table-invoice">
                  <thead>
                     <tr>
                        <th>ITEM DESCRIPTION</th>
                        <th class="text-center" width="10%">PRICE</th>
                        <th class="text-center" width="10%">QUANTITY</th>
                        <th class="text-right" width="20%">TOTAL</th>
                     </tr>
                  </thead>
                  <tbody>
                     <tr>
                        <td>
                           <!-- <span class="text-inverse">Website design &amp; development</span><br>
                           <small>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed id sagittis arcu.</small> -->
                           {{ $value->item_description }}
                        </td>
                        <td class="text-center">{{ $value->currency." " . $value->product_price}}</td>
                        <td class="text-center">{{ ($value->qty) }}</td>
                        <td class="text-right">{{$value->currency." " .($value->grand_total) }}</td>
                     </tr>
                    
                  </tbody>
               </table>
            </div>
            <!-- end table-responsive -->
            <!-- begin invoice-price -->
            <div class="invoice-price">
               <div class="invoice-price-left">
                  <div class="invoice-price-row">
                     <div class="sub-price">
                        <small>SUBTOTAL</small>
                        <span class="text-inverse">{{$value->currency." " . $value->grand_total }}</span>
                     </div>
                     <div class="sub-price">
                        <i class="fa fa-plus text-muted"></i>
                     </div>
                     <div class="sub-price">
                        @if ($value->taxable_value == '')
                           
                           <small>TAX ( 0 )</small>
                           <span class="text-inverse">{{$value->currency." " . 0 }}</span>
                        @else
                           <small>Tax ({{ $value->taxable_value }})</small>
                           <span class="text-inverse">{{$value->currency." " . $value->taxable_value }}</span>
                        @endif
                     </div>
                  </div>
               </div>
               <div class="invoice-price-right">
                  <small>GRAND TOTAL</small> <span class="f-w-600">{{$value->currency." " .($value->grand_total) }}</span>
               </div>
            </div>
            <!-- end invoice-price -->
         </div>
         @endforeach
         
      </div>
   </div>
</div>

@stop

@section('js')
<script>
   $(document).ready(function(){
      $('#Export_to_pdf').click(function(e){
         e.preventDefault();
         var url = $(location).attr('href');
         var invoice_no = $('#pid').val();
         // var all = $('#all').val();
         // alert(all);
         // alert(id);
         // alert(url);

         $.ajax({
            method: 'POST',
            url: "{{ url('/invoice/export-pdf')}}",
            data:{ 
               'url':url,
               'invoice_no':invoice_no,
               "_token": "{{ csrf_token() }}",
               },
            success: function(response) {

               window.location.href = '/invoice/download/'+invoice_no;
               alert('Download pdf successfully');
            }
         });
      });
   });
</script>
@stop
