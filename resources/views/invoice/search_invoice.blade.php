@extends('adminlte::page')
@section('title', 'Search Invoice')

@section('css')
<link rel="stylesheet" href="/css/styles.css">

@stop

@section('content_header')
<div class="row">
    <h1 class="m-0 text-dark col">Invoice management</h1>
    <h2 class="mb-4 text-right col">
        <!-- <a href=""> -->
            <x-adminlte-button label="Selected Download" id="selected-download" theme="primary" icon="fas fa-file-download" class="btn-sm"/>
        <!-- </a> -->
        <!-- <a href="download-all">  -->
            <x-adminlte-button label="Selected Print" id='select_print' theme="primary" icon="fas fa-print" class="btn-sm" />
        <!-- </a> -->
        
    </h2>
</div>
@stop
@section('content')

<div class="container search-box">
    <div class="row">
        <div class="col"></div>
        <div class="col-4">
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
                        <input type="text" class="form-control float-right datepicker" name='invoice_date' autocomplete="off" id="invoice_date">
                        <x-adminlte-button label="Search" theme="primary" icon="fas fa-search" id="search" class="btn-sm ml-2" />
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
            $('#showTable').show();
            let invoice_date = $('#invoice_date').val();
            alert(invoice_date);
            $.ajax({
                method: 'POST',
                url: "{{ url('/invoice/select-invoice')}}",
                data:{ 
                "invoice_date": invoice_date,
                "_token": "{{ csrf_token() }}",
                },
                success: function(response) {
                    console.log(response);
                    let table ="<table class=table table-bordered table-striped text-center ><thead><tr class='text-bold bg-info'><td>Check</td> <td>Invoice No.</td><td>Invoice Date</td><td>Channel</td><td>Shipped By</td><td>Awb No</td><td>Arn NO.</td><td>Hsn Code</td><td>Quantity</td><td>Product Price</td><td class='text-center'>Action</td></tr></thead><tbody> ";
                    $.each(response, function(i, response){
                        table +="<tr><td><div class=pl-2><input class=check_options type=checkbox value="+ response.id +" name=options[] ></div></td><td>"+response.invoice_no+"</td><td>"+response.invoice_date+"</td><td>"+response.channel+"</td><td>"+response.shipped_by+"</td><td>"+response.awb_no+"</td><td>"+response.arn_no+"</td><td>"+response.hsn_code+"</td><td>"+response.qty+"</td><td>"+response.product_price+"</td><td><div class='d-flex'><a href=/invoice/convert-pdf/"+ response.id + " class='edit btn btn-success btn-sm' target=_blank><i class='fas fa-eye'></i> View </a><div class='d-flex pl-2'><a href=/invoice/download-direct/"+ response.id +" class='edit btn btn-info btn-s'><i class='fas fa-download'></i> Download </a></td> </tr>";
                    });
                    $('#showTable').html(table);
                alert('Export pdf successfully');
                }
            });
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