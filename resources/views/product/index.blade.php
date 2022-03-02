@extends('adminlte::page')
@section('title', 'Import')

@section('content_header')
<h1 class="m-0 text-dark">Amazon Data</h1>
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
                <a href="{{ route('product.fetch.amazon') }}">
                    <x-adminlte-button label="Fetch Catalog From Amazon" theme="primary" icon="fas fa-file-export" id="exportUniversalTextiles"/>
                </a>
                <!-- <a href="{{ route('amazon.getPricing') }}">
                    <x-adminlte-button label="Get Pricing From Amazon" theme="primary" icon="fas fa-file-export" id="exportUniversalTextiles"/>
                </a> -->
            </h2>
            <table class="table table-bordered yajra-datatable table-striped">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Asin</th>
                            <th>Source</th>
                            <th>Name</th>
                            <th>Dimention</th>
                            <th>Weight</th>
                            <th>Price</th>
                        
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

let yajra_table = $('.yajra-datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ url('product/amazon_com') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'asin', name: 'asin'},
                {data: 'source', name: 'source'},
                {data: 'label', name: 'label'},
                {data: 'item_dimensions', name: 'item_dimensions'},
                {data: 'weight', name: 'weight'},
                {data: 'amount', name: 'amount'}, 
           
                
            ]
        });
    
</script>   
@stop