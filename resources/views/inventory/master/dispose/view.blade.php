@extends('adminlte::page')

@section('title', 'Inventory Stocks')

@section('css')

    <link rel="stylesheet" href="/css/styles.css">

@stop

@section('content_header')
    <h1 class="m-0 text-dark"> Stocks</h1>

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
                        <th>ID</th>
                        <th>Warehouse</th>
                        <th>ASIN</th>
                        <th>Item Name</th>
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
        $(function() {

            let yajra_table = $('.yajra-datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('inventory.stocks') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'warehouse_name',
                        name: 'warehouse_name'
                    },                
                    
                    {
                        data: 'asin',
                        name: 'asin'
                        
                    },           
                    {
                        name: 'item_name',
                        data: 'item_name'
                    },           
                    {
                        data: 'quantity',
                        name: 'quantity'
                    },           
                ]
            });
        });
    </script>
@stop






