@extends('adminlte::page')
@section('title', 'Search Label')

@section('css')
<link rel="stylesheet" href="/css/styles.css">

@stop
@section('content_header')
<div class="row">
    <h1 class="m-0 text-dark col">Label management</h1>
    <h2 class="mb-4 text-right col"></h2>
    <label>
        Search:<input type="text" id="Searchbox" placeholder="search invoice">
    </label>
        <!-- <a href=""> -->
            <!-- <x-adminlte-button label="Selected Download" id="selected-download" theme="primary" icon="fas fa-file-download" class="btn-sm"/> -->
        <!-- </a> -->
        <!-- <a href="download-all">  -->
            <!-- <x-adminlte-button label="Selected Print" id='select_print' theme="primary" icon="fas fa-print" class="btn-sm" /> -->
        <!-- </a> -->
        
    
</div>
@stop

@section('content')

<div class="container-fluid label-search-box">
    <div class="row">
        <div class="col"></div>
        <div class="col-5">
            <form action="">
                @csrf
                <div class="form-group">
                    <label>Label Date:</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="far fa-calendar-alt"></i>
                            </span>
                        </div>
                        <input type="text" class="form-control float-right datepicker" name="label_date" placeholder="Select Date Range" autocomplete="off" id="label_date">
                        <x-adminlte-button label="Search" theme="primary" icon="fas fa-search" id="search" class="btn-sm ml-2" />
                        <x-adminlte-button label="Download Selected" id='download_selected' theme="primary" icon="fas fa-download" class="btn-sm ml-2" />
                        <x-adminlte-button label="Print Selected" id='print_selected' theme="primary" icon="fas fa-print" class="btn-sm ml-2" />
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
        // alert('working');
       var test = $(".datepicker").daterangepicker({
        autoUpdateInput: false,
        locale: {
            format: "YYYY-MM-DD"
        },
       });

       $('.datepicker').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
        });

        $('.datepicker').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });
        
        $('#search').click(function(){
            if(($('.datepicker').val() == ''))
            {
                alert('Please Choose Date');
            }
                $('#showTable').show();
                let label_date = $('#label_date').val();
                alert(label_date);
                $.ajax({
                    method: 'POST',
                    url: "{{ url('/label/select-label')}}",
                    data:{ 
                    "invoice_date": label_date,
                    "_token": "{{ csrf_token() }}",
                    },
                    success: function(response) {
                        console.log(response);
                        // let table ="<table id='checkTable' class=table table-bordered table-striped text-center >";
                        // table += "<thead><tr class='text-bold bg-info'><td>Selected</td> <td>Invoice No.</td><td>Invoice Date</td><td>Channel</td><td>Shipped By</td><td>Awb No</td><td>Arn NO.</td><td>Hsn Code</td><td>Quantity</td><td>Product Price</td><td class='text-center'>Action</td></tr></thead><tbody> ";
    
                        // $.each(response, function(i, response){
                        //     let invoice_id = response.invoice_no.replaceAll(/-/g, '_');
            
                        //     table +="<tr class='"+invoice_id+"'><td><input class='check_options' type='checkbox' value="+ response.id +" name='options[]' id='checkid"+response.id+"'></td><td>"+response.invoice_no+"</td><td>"+response.invoice_date+"</td><td>"+response.channel+"</td><td>"+response.shipped_by+"</td><td>"+response.awb_no+"</td><td>"+response.arn_no+"</td><td>"+response.hsn_code+"</td><td>"+response.qty+"</td><td>"+response.product_price+"</td><td><div class='d-flex'><a href=/invoice/convert-pdf/"+ response.id +" class='edit btn btn-success btn-sm' target='_blank'><i class='fas fa-eye'></i> View </a><div class='d-flex pl-2'><a href=/invoice/download-direct/"+ response.id +" class='edit btn btn-info btn-sm'><i class='fas fa-download'></i> Download </a></td> </tr>";
                        // });
                        // $('#showTable').html(table);
                    // alert('Export pdf successfully');
                    }
                });
            
        });
    });
</script>
@stop