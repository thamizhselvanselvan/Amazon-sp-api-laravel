@extends('adminlte::page')

@section('title', 'create Shipment')

@section('css')

<link rel="stylesheet" href="/css/styles.css">

@stop

@section('content_header')
<h1 class="m-0 text-dark">Create Shipment</h1>
@stop
@section('content')
<!-- 
<div class="row">
    <div class="col">
        <a href="{{ route('shipments.index') }}" class="btn btn-primary">
            <i class="fas fa-long-arrow-alt-left btn-sm"></i> Back
        </a>
    </div>
</div> -->
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
<form action="{{ route('BOE.Export.Filter') }}" method="POST" id="boe_export_filter" class="row">
    @csrf

    <div class="col-2">
        <div class="form-group">
            <label>Enter ASIN:</label>
            <div class="input-group">
                <div class="input-group-prepend">
                </div>
                <input type="text" class="form-control float-right datepicker" name='upload_asin' autocomplete="off" id="upload_asin">
            </div>
        </div>
    </div>
    <div class="col-2">
        <div style="margin-top: 2.1rem;">
            <x-adminlte-button label="Search" theme="primary" icon="fas fa-search" id="search" class="btn-sm" />
        </div>
    </div>
</form>

<div class="row">

</div>
<br>
<table class="table table-bordered yajra-datatable table-striped" id="report_table">
    <thead>
        <tr>
            <td>S/N</td>
            <td>asin</td>
            <td>Seller ID</td>
            <td>Item Name</td>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>
@stop
<!-- 
@section('js')

<script type="text/javascript">
   $("#search").on('click', function(e) {
        $("#report_table").show();
                let yajra_table = $('.yajra-datatable').DataTable({

                        destroy: true,
                        processing: true,
                        serverSide: true,
                        ajax: ("{{ url('BOE/Export/view') }}",


                            columns: [{
                                    data: 'DT_RowIndex',
                                    name: 'DT_RowIndex',
                                    orderable: false,
                                    searchable: false
                                },
                                {
                                    data: 'asin',
                                    name: 'asin'
                                },
                                {
                                    data: 'seller_id',
                                    name: 'seller_id',
                                    orderable: false,
                                },
                                {
                                    data: 'item_name',
                                    name: 'item_name',
                                    orderable: false,
                                }
                            ],

                        });
                });
</script>
@stop -->