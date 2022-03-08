@extends('adminlte::page')

@section('title', 'Amazon other product')

@section('content_header')
<h1 class="m-0 text-dark">Amazon Other Products</h1>
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
                <h2 class="mb-4">

                    <a href="{{route('export.other-product')}}">
                        <x-adminlte-button label="Product Export" theme="primary" icon="fas fa-file-export"/>
                    </a>

                </h2>
               
                <table class="table table-bordered yajra-datatable table-striped">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>ASIN</th>
                            <th>SKU</th>
                            <th>Title</th>
                            <th>List Price</th>
                            <th>Price INR</th>
                            <th>List Price INR</th>
                            <th>Weight</th>
                            <th>Availability</th>
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
            ajax: "{{ url('other-product/amazon_com') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'asin', name: 'asin'},
                {data: 'sku', name: 'sku'},
                {data: 'title', name: 'title'},
                {data: 'list_price', name: 'list_price'},
                {data: 'price_inr', name: 'price_inr'},
                {data: 'list_price_inr', name: 'list_price_inr'}, 
                {data: 'weight', name: 'weight'},
                {data: 'availability', name: 'availability'},
                
            ]
        });
     
</script>   
@stop