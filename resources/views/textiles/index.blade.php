@extends('adminlte::page')
@section('title', 'Import')

@section('content_header')
<h1 class="m-0 text-dark">Imported Data</h1>
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
                {data: 'textiles', name: 'textiles'},
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
     });


</script>   
@stop