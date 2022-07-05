@extends('adminlte::page')
@section('title', 'Search Invoice')

@section('content_header')
<div class="row">
    <h1 class="m-0 text-dark col">Invoice Management</h1>
    <!-- <h2 class="mb-4 text-right col"></h2> -->
    <label>
        Search:<input type="text" id="Searchbox" placeholder="search invoice" autocomplete="off" />
    </label> 
</div>
<div class="row">
    <div class="col">
        <a href="{{ route('invoice.index') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-long-arrow-alt-left"></i> Back
        </a>
    </div>
</div>
@stop
@section('content')

<div class="container-fluid search-box">
    <div class="row">
        <div class="col"></div>
        <div class="col-2">
            <form action="">
                @csrf
                <div class="form-group">
                    <x-adminlte-select label="Mode: " name="mode" id="mode" class="float-right">
                        <option value="">select mode</option>
                        @foreach ($mode as $value)
                            <option value="{{$value->mode}} ">{{$value->mode}}</option>
                        @endforeach
                    </x-adminlte-select>
                </div>
            </form>
        </div>
        <div class="col">
            <form action="">
                @csrf
               
                <div class="form-group">
                    <label>Invoice Date:</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="far fa-calendar-alt"></i>
                            </span>
                        </div>
                        <input type="text" class="form-control float-right datepicker" name='invoice_date' placeholder="Select Date Range" autocomplete="off" id="invoice_date">
                        <x-adminlte-button label="Search" theme="primary" icon="fas fa-search" id="search" class="btn-sm ml-2" />
                        <x-adminlte-button label="Download Selected" id="selected-download" theme="primary" icon="fas fa-download" class="btn-sm ml-2"/>
                        <x-adminlte-button label="Print Selected" id='select_print' theme="primary" icon="fas fa-print" class="btn-sm ml-2" />
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div id = "showTable"></div>

@stop

@section('js')
<script type="text/javascript">

    $(document).ready(function(){
        //start search invoice
        $("#Searchbox").on('keyup', function() {
            let self = $(this);
            let invoice_no = $.trim(self.val());
            let invoice_no_re = invoice_no.replaceAll(/-/g, '_');
            let tr = $("."+invoice_no_re);
            let table = $("#checkTable");

            $(tr.children().children()[0]).prop('checked', true);
            $(tr).addClass('bg-warning');
            tr.prependTo(table);
        });
        //end search invoice

        $('#showTable').hide();
        $(".datepicker").daterangepicker({
            autoUpdateInput: false,
            locale: {
                format: 'YYYY-MM-DD',
            },
        });
        $('.datepicker').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
        });

        $('.datepicker').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });
        
        $('#search').click(function(){

            if($('#mode').val() == ''){
                alert('Please Choose Mode');
            }
            else if(($('.datepicker').val() == '')){
                alert('Please Choose Date');
            }
            else{

                $('#showTable').show();
                let invoice_mode = $('#mode').val();
                let invoice_date = $('#invoice_date').val();
                // alert(invoice_date);
                $.ajax({
                    method: 'POST',
                    url: "{{ url('/invoice/select-invoice')}}",
                    data:{ 
                    "invoice_date": invoice_date,
                    "invoice_mode": invoice_mode,
                    "_token": "{{ csrf_token() }}",
                    },
                    success: function(response) {
                        // console.log(response);
                        let table ="<table id='checkTable' class='table table-bordered table-striped text-center' >";
                        table += "<thead><tr class='text-bold bg-info'><th>Selected</th> <th>INVOICE NO.</th><th>INVOICE DATE</th><th>MODE</th><th>CHANNEL</th><th>SHIPPED BY</th><th>AWB NO.</th><th>STORE NAME</th><th>BILL TO NAME</th><th>SHIP TO NAME</th><th>SKU</th><th>QTY</th><th>PRODUCT PRICE</th><th class='text-center'>ACTION</th></tr></thead><tbody> ";
    
                        $.each(response, function(i, response){
                            let invoice_id = response.invoice_no.replaceAll(/-/g, '_');
            
                            table +="<tr class='"+invoice_id+"'><td><input class='check_options' type='checkbox' value="+ response.id +" name='options[]' id='checkid"+response.id+"'></td><td>"+response.invoice_no+"</td><td>"+response.invoice_date+"</td><td>"+response.mode+"</td><td>"+response.channel+"</td><td>"+response.shipped_by+"</td><td>"+response.awb_no+"</td><td>"+response.store_name+"</td><td>"+response.bill_to_name+"</td><td>"+response.ship_to_name+"</td><td>"+response.sku+"</td><td>"+response.qty+"</td><td>"+response.currency +' '+ response.product_price+"</td><td><div class='d-flex'><a href=/invoice/convert-pdf/"+ response.invoice_no +" class='edit btn btn-success btn-sm' target='_blank'><i class='fas fa-eye'></i> View </a><div class='d-flex pl-2'><a href=/invoice/download-direct/"+ response.invoice_no +" class='edit btn btn-info btn-sm'><i class='fas fa-download'></i> Download </a>";
                            table +="<div class='d-flex pl-2'><a href=/invoice/edit/"+ response.invoice_no +" class='edit btn btn-primary btn-sm'><i class='fas fa-edit'></i> Edit </a></td> </tr>"
                        });
                        $('#showTable').html(table);
                    // alert('Export pdf successfully');
                    }
                });
            }
            
        });

        $('#selected-download').click( function() {
            var url = $(location).attr('href');
            let id = '';
            let count = 0;
            let arr = '';
            $("input[name='options[]']:checked").each(function() {
                if (count == 0) {
                    id += $(this).val();
                } else {
                    id += '-' + $(this).val();   
                }
                
                count++; 
            });
            // alert(id);
            $.ajax({
                method: 'POST',
                url: "{{ url('/invoice/select-download')}}",
                data:{ 
                'id':id,
                "_token": "{{ csrf_token() }}",
                },
                success: function(response) {
                    arr += response;
                    window.location.href = '/invoice/zip-download/'+arr;
                // alert('Export pdf successfully');
                }
            });
                
        });

        $('#select_print').click( function() {
            var url = $(location).attr('href');
            let id = '';
            let count = 0;
            let arr = '';
            $("input[name='options[]']:checked").each(function() {
                if (count == 0) {
                    id += $(this).val();
                } else {
                    id += '-' + $(this).val();   
                }
                count++; 
                window.location.href = '/invoice/selected-print/'+id;
            });
            // alert(id);
        });
    });
</script>
@stop