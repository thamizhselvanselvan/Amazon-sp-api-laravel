@extends('adminlte::page')
@section('title', 'Import')

@section('content_header')
<h1 class="m-0 text-dark">Imported Data</h1>
@stop

@section('content')
@csrf

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
                <h2 class="mb-4">
                <a >
                    <x-adminlte-button label="Import Universal Textiles" theme="primary" icon="fas fa-file-import" id="importUniversalTextiles"/>
                </a>
                <a href="{{ route('export.csv') }}">
                    <x-adminlte-button label="Export Universal Textiles" theme="primary" icon="fas fa-file-export" id="exportUniversalTextiles"/>
                </a>
            </h2>
                <table class="table table-bordered yajra-datatable table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>ID </th>
                            <th>Ean</th>
                            <th>Brand</th>
                            <th>Title</th>
                            <th>Size</th>
                            <th>Color</th>
                            <th>Transer Price</th>
                            <th>Shipping weight</th>
                            <th>Product Type</th>
                            <th>Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
            </table>
        </div>
    </div>
@stop

@section('js')
<script type="text/javascript">
     $(function () {

            let yajra_table = $('.yajra-datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ url('textiles') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'textile_id', name: 'textile_id'},
                {data: 'ean', name: 'ean'},
                {data: 'brand', name: 'brand'},
                {data: 'title', name: 'title'},
                {data: 'size', name: 'size'},
                {data: 'color', name: 'color'}, 
                {data: 'transfer_price', name: 'transfer_price'},
                {data: 'shipping_weight', name: 'shipping_weight'}, 
                {data: 'product_type', name: 'product_type'},
                {data: 'quantity', name: 'quantity'},
               
            ]
        });
     

     $(document).on('click', '#importUniversalTextiles', function(){

        $.ajax({
            method: 'post',
            url: '/import-csv',
            data:{
                "_token": "{{ csrf_token() }}",
                "_method": 'POST',
            },
            success: function() {
                alert('success');
                yajra_table.ajax.reload();
                
            }
         })      
    });
});

</script>   
@stop