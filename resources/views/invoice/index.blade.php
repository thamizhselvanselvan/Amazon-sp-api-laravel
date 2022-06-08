@extends('adminlte::page')
@section('title', 'Invoice')

@section('content_header')
<div class="row">
    <h1 class="m-0 text-dark col">Invoice Management</h1>
    <h2 class="mb-4 text-right col">
        <a href="upload">
            <x-adminlte-button label="Upload Invoice Excel" theme="primary" icon="fas fa-file-upload" class="btn-sm" />
        </a>
        <!-- <a href="Export/view">
            <x-adminlte-button label="Download Invoice PDF" theme="primary" icon="fas fa-file-download" class="btn-sm" />
        </a> -->
        <!-- <a href="Download">
            <x-adminlte-button label="Download CSV file" theme="primary" icon="fas fa-file-download" />
        </a> -->
        <!-- <a href=""> -->
            <!-- <x-adminlte-button label="Convert pdf" id='convert_pdf' theme="primary" icon="fas fa-check-circle" class="btn-sm" /> -->
        <!-- </a> -->
    </h2>
</div>
@stop

@section('content')

<div class="row">
    <div class="col">

        <div class="alert_display">
            @if ($message = Session::get('success'))
            <div class="alert alert-success alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $message }}</strong>
            </div>
            @endif
        </div>
    </div>
</div>

<table class="table table-bordered yajra-datatable table-striped">
    <thead>
        <tr class="text-bold bg-info">
            <td>S/N</td> 
            <td>Invoice No.</td>
            <td>Invoice Date</td>
            <td>Order No</td>
            <td>Order Date</td>
            <td>Channel</td>
            <td>Shipped By</td>
            <td>Awb No</td>
            <!-- <td>arn</td>
            <td>store_name_add</td>
            <td>bill_to_add</td>
            <td>ship_to_add</td>
            <td>sr_no</td>
            <td>item_description</td>
            <td>hsn_code</td> -->
            <td>Quantity</td>
            <td>Currency</td>
            <td>Product Price</td>
            <td>Action</td>
            <!-- <td>taxable_value</td>
            <td>total_including_taxes</td>
            <td>grand_total</td> -->

        </tr>
    </thead>
    <tbody>
    </tbody>
    

</table>
@stop

@section('js')
<script>

    let yajra_table = $('.yajra-datatable').DataTable({

    processing: true,
    serverSide: true,
    ajax: "{{ url('/invoice/manage') }}",
    columns: [{
        data: 'DT_RowIndex',
        name: 'DT_RowIndex',
        orderable: false,
        searchable: false
        },
        {
            data: 'invoice_no',
            name: 'invoice_no'
        },
        {
            data: 'invoice_date',
            name: 'invoice_date',
            orderable: false,
        },
        {
            data: 'order_no',
            name: 'order_no',
            orderable: false,
        },
        {
            data: 'order_date',
            name: 'order_date',
        },
        {
            data: 'channel',
            name: 'channel'
        },
        {
            data: 'shipped_by',
            name: 'shipped_by',
        },

        {
            data: 'awb_no',
            name: 'awb_no',
        },
        {
            data: 'qty',
            name: 'qty'
        },
        {
            data: 'currency',
            name: 'currency'
        },
        {
            data: 'product_price',
            name: 'product_price'
        },
        
        {
            data: 'action',
            name: 'action'
        },

    ],

    });

    // $('').on('click', function() {

    //     let id = '';
    //     let count = 0;
    //     $("input[name='options[]']:checked").each(function() {
    //         if (count == 0) {
    //             id += $(this).val();
    //         } else {
    //             id += '-' + $(this).val();
    //         }
    //         count++;
        
    //     });
    //     // window.location.href = '/invoice/convert-pdf/'+id;

    // });

    $(document).ready(function(){
      $('#Export_to_pdf').click(function(e){
         e.preventDefault();
         var url = $(location).attr('href');
         var id = $('#pid').val();
         var all = $('#all').val();

         alert(working);
         // alert(alert);
         // alert(url);

            $.ajax({
                method: 'POST',
                url: "{{ url('/invoice/export-pdf')}}",
                data:{ 
                'url':url,
                'id':id,
                'total' : all,
                "_token": "{{ csrf_token() }}",
                },
                success: function(response) {

                window.location.href = '/invoice/download/'+id;
                alert('Export pdf successfully');
                }
            });
        });
    });

</script>
@stop