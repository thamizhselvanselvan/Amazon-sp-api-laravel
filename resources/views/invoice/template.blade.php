@extends('adminlte::page')

@section('title', 'Invoice')

@section('css')

    <link rel="stylesheet" href="/css/styles.css">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">
@stop
@section('content_header')

@stop


@section('content')


<div class="container">
   <div class="col-md-12">
      <div class="invoice">
         <!-- begin invoice-company -->
         <div class="invoice-company text-inverse f-w-600">
            <span class="pull-right hidden-print">
            <a href="javascript:void(0);" class="btn btn-sm btn-white m-b-10 p-l-5" id="Export_to_pdf"><i class="fa fa-file t-plus-1 text-danger fa-fw fa-lg"></i> Export as PDF</a>
            <a href="javascript:;" onclick="window.print()" class="btn btn-sm btn-white m-b-10 p-l-5"><i class="fa fa-print t-plus-1 fa-fw fa-lg"></i> Print</a>
            </span>
            Company Name, Inc
         </div>
         <!-- end invoice-company -->
         <!-- begin invoice-header -->
         @foreach ($data as $key => $value )
             
         
         <div class="invoice-header">
            <div class="invoice-from">
               <small>from</small>
               <address class="m-t-5 m-b-5">
                  <strong class="text-inverse">Twitter, Inc.</strong><br>
                  <!-- Street Address<br>
                  City, Zip Code<br>
                  Phone: (123) 456-7890<br>
                  Fax: (123) 456-7890 -->
                  {{ $value->store_name_add }}
               </address>
            </div>
            
            <div class="invoice-to">
               <small>to</small>
               <address class="m-t-5 m-b-5">
                  <strong class="text-inverse">Company Name</strong><br>
                  <!-- Street Address<br>
                  City, Zip Code<br>
                  Phone: (123) 456-7890<br>
                  Fax: (123) 456-7890 -->
                  {{ $value->ship_to_add }}
               </address>
            </div>
            <div class="invoice-date">
               <small>Invoice / July period</small>
               <div class="date text-inverse m-t-5">{{$value->invoice_date}}</div>
               <div class="invoice-detail">
                  {{ $value->invoice_no }}<br>
                  Services Product
               </div>
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
                        <th class="text-center" width="10%">Price</th>
                        <th class="text-center" width="10%">Quantity</th>
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
                     <!-- <tr>
                        <td>
                           <span class="text-inverse">Branding</span><br>
                           <small>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed id sagittis arcu.</small>
                        </td>
                        <td class="text-center">$50.00</td>
                        <td class="text-center">40</td>
                        <td class="text-right">$2,000.00</td>
                     </tr>
                     <tr>
                        <td>
                           <span class="text-inverse">Redesign Service</span><br>
                           <small>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed id sagittis arcu.</small>
                        </td>
                        <td class="text-center">$50.00</td>
                        <td class="text-center">50</td>
                        <td class="text-right">$2,500.00</td>
                     </tr> -->
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
                        <small>Tax ({{ $value->taxable_value }})</small>
                        <span class="text-inverse">{{$value->currency." " . $value->taxable_value }}</span>
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
         <!-- end invoice-content -->
         <!-- begin invoice-note -->
         <div class="invoice-note">
            * Make all cheques payable to [Your Company Name]<br>
            * Payment is due within 30 days<br>
            * If you have any questions concerning this invoice, contact  [Name, Phone Number, Email]
         </div>
         <!-- end invoice-note -->
         <!-- begin invoice-footer -->
         <div class="invoice-footer">
            <p class="text-center m-b-5 f-w-600">
               THANK YOU FOR YOUR BUSINESS
            </p>
            <p class="text-center">
               <span class="m-r-10"><i class="fa fa-fw fa-lg fa-globe"></i> matiasgallipoli.com</span>
               <span class="m-r-10"><i class="fa fa-fw fa-lg fa-phone-volume"></i> T:016-18192302</span>
               <span class="m-r-10"><i class="fa fa-fw fa-lg fa-envelope"></i> rtiemps@gmail.com</span>
            </p>
         </div>
         <!-- end invoice-footer -->
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
         alert(url);

         $.ajax({
            method: 'POST',
            url: "{{ url('/invoice/export-pdf')}}",
            data:{ 
               'id':url,
               "_token": "{{ csrf_token() }}",
               },
            // cache: false,
            // contentType: false,
            // processData: false,
            // dataType: 'json',
            success: function(response) {
               console.log(response);
               alert('Export Pdf Successfully');
               
            }
         });
      });
   });
</script>
@stop