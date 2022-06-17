@extends('adminlte::page')
@section('title', 'Label')

@section('content_header')
<div class="row">
    <h1 class="m-0 text-dark col">Label Management</h1>
    <h2 class="mb-4 text-right col">
        <a href="excel/template">
            <x-adminlte-button label="Download Template" theme="primary" icon="fas fa-file-download" class="btn-sm" />
        </a>
        <a href="upload">
            <x-adminlte-button label="Upload Excel Sheet" theme="primary" icon="fas fa-file-upload" class="btn-sm" />
        </a>
        <a href="download-all">
            <x-adminlte-button label="Download Label" id='download_pdf' theme="primary" icon="fas fa-check-circle" class="btn-sm" />
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
<div class="pl-2">
<table class="table table-bordered yajra-datatable table-striped text-center">
    <thead>
        <tr class="text-bold bg-info">
            <td>S/N</td> 
            <td>Status.</td>
            <td>Order No.</td>
            <td>Awb No.</td>
            <td>Select All <br><input class="check_all" type="checkbox" value='' name="options[]" id="check_all" ></div> </td>
            <td>Action</td>
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
ajax: "{{ url('/label/manage') }}",
pageLength: 1000,
columns: [{
    data: 'DT_RowIndex',
    name: 'DT_RowIndex',
    orderable: false,
    searchable: false
    },
    {
        data: 'status',
        name: 'status'
    },
    {
        data: 'order_no',
        name: 'order_no'
    },
    {
        data: 'awb_no',
        name: 'awb_no',
    },
    {
        data: 'check_box',
        name: 'check_box',
    },
    {
        data: 'action',
        name: 'action',
    },
   

],
});

</script>
@stop