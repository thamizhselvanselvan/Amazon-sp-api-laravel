@extends('adminlte::page')
@section('title', 'Import')

@section('content_header')
<div class="row">
    <h1 class="m-0 text-dark">ASIN Price Details</h1>
    <div class="col text-right">
        <a href="/seller/price/export">
            <x-adminlte-button label="Pricing CSV Export" theme="primary" icon="fas fa-file-import" id='pricing' />
        </a>
        <a href='/seller/pricing/download'>
            <x-adminlte-button label="Download Price CSV" theme="primary" icon="fas fa-file-download" />
        </a>
    </div>
</div>
@stop

@section('content')
@csrf

<table class="table table-bordered yajra-datatable table-striped">
    <thead>
        <tr class="length">
            <th>S/N</th>
            <th>ASIN</th>
            <th>Is Fulfilment By Amazon</th>
            <th>Price</th>
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
        ajax: "{{ url('seller/price/details') }}",
        columns: [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false
            },
            {
                data: 'asin',
                name: 'asin',
                orderable: false
            },
            {
                data: 'is_fulfilment_by_amazon',
                name: 'is_fulfilment_by_amazon',
                orderable: false
            },
            {
                data: 'price',
                name: 'price',
                orderable: false,
                searchable: false
            },

        ]
    });
</script>
@stop