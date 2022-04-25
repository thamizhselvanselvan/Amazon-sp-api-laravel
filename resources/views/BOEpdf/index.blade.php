@extends('adminlte::page')

@section('title', 'BOE Master')

@section('content_header')
<div class="row">
    <h1 class="m-0 text-dark col">Bill OF Entry</h1>
    <h2 class="mb-4 text-right col">
        <a href="upload">
            <x-adminlte-button label="Upload BOE" theme="primary" icon="fas fa-file-upload" class="btn-sm" />
        </a>
        <a href="Export/view">
            <x-adminlte-button label="Export to CSV" theme="primary" icon="fas fa-file-export" class="btn-sm" />
        </a>
        <a href="Download">
            <!-- <x-adminlte-button label="Download CSV file" theme="primary" icon="fas fa-file-download" /> -->
        </a>
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
    <!-- <tfoot>
        <tr>
            <th>Name</th>
            <th>Position</th>
            <th>Office</th>
            <th>Age</th>
            <th>Start date</th>
            <th>Salary</th>
        </tr>
    </tfoot> -->
    <thead>
    <tr>
            <td>S/N</td>
            <!-- <td>Company Name</td> -->
            <td>AWB No.</td>
            <td>Courier Registration Number</td>
            <td>Name of the Authorized Courier</td>
            <td>Name of Consignor</td>
            <td>Name of Consignee</td>
            <td>BOE Booking Rate</td>
            <td>Duty</td>
            <td>SW Srchrg</td>
            <td>Insurance</td>
            <td>IGST</td>
            <td>Total(Duty+Cess+IGST)</td>
            <td>Interest</td>
            <td>CBX II No</td>
            <td>HSN Code Qty</td>
            <td>Qty</td>
            <td>Date of Arrival</td>
        </tr>
    </thead>
    <tbody>
    </tbody>
   
</table>




@stop

@section('js')

<script type="text/javascript">
    let yajra_table = $('.yajra-datatable').DataTable({

        processing: true,
        serverSide: true,

        ajax: "{{ url('BOE/index') }}",
        columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'hawb_number',
                    name: 'hawb_number'
                },
                {
                    data: 'courier_registration_number',
                    name: 'courier_registration_number',
                    orderable: false,
                },
                {
                    data: 'name_of_the_authorized_courier',
                    name: 'name_of_the_authorized_courier',
                    orderable: false,
                },
                {
                    data: 'name_of_consignor',
                    name: 'name_of_consignor',
                },
                {
                    data: 'name_of_consignee',
                    name: 'name_of_consignee'
                },
                {
                    data: 'rateof_exchange',
                    name: 'rateof_exchange',
                    orderable: false,
                    searchable: false
                },

                {
                    data: 'duty',
                    name: 'duty',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'swsrchrg',
                    name: 'swsrchrg',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'insurance',
                    name: 'insurance',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'igst',
                    name: 'igst',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'duty_rs',
                    name: 'duty_rs',
                    
                },
                {
                    data: 'interest',
                    name: 'interest',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'cbe_number',
                    name: 'cbe_number',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'ctsh',
                    name: 'ctsh',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'quantity',
                    name: 'quantity'
                },
                {
                    data: 'date_of_arrival',
                    name: 'date_of_arrival'
                },

            ]
    });
//     $('.yajra-datatable tfoot th').each( function () {
//         var title = $(this).text();
//         $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
//     } );

//     var table = $('.yajra-datatable').DataTable({
//         initComplete: function () {
//             // Apply the search
//             this.api().columns().every( function () {
//                 var that = this;
 
//                 $( 'input', this.footer() ).on( 'keyup change clear', function () {
//                     if ( that.search() !== this.value ) {
//                         that
//                             .search( this.value )
//                             .draw();
//                     }
//                 } );
//             } );
//         }
//     });
    	
// $('.yajra-datatable tfoot tr').appendTo('.yajra-datatable thead');
// var table = $('.yajra-datatable').DataTable({
//     "columnDefs": [
//         { "searchable": false, "targets": [1,2] }
//     ],
//     });

// $('.yajra-datatable').api().column( [0,1,2] );
</script>
@stop