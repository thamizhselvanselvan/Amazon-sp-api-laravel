@extends('adminlte::page')

@section('title', 'SNT Inward')

@section('css')

<link rel="stylesheet" href="/css/styles.css">
<style>
    .table td {
        padding: 0;
        padding-left: 5px;
    }

    .table th {
        padding: 2;
        padding-left: 5px;
    }
</style>
@stop

@section('content_header')
<div class="row">
    <div class="col-2">
        <div style="margin-top:-0.1rem">
            <a href="{{route('shipntrack.inward.create')}}">
                <x-adminlte-button label="Create Shipment" class="btn-sm" theme="primary" icon="fas fa-plus" />
            </a>
        </div>
    </div>
    <div class="col-2"></div>
    <div class="col-6">

        <h1 class="m-0 text-dark"> SNT Inward Shipment</h1>
    </div>
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
            @if($message = Session::get('error'))
            <div class=" alert alert-danger alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $message }}</strong>
            </div>
            @endif
       
            <div class="alert_display">
                @if (request('success'))
                <div class="alert alert-success alert-block">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <strong>{{request('success')}}</strong>
                </div>
                @endif
            </div>
         
        </div>

        <div id="showTable" class="">
            <table class='table table-bordered yajra-datatable table-striped text-center'>
                <thead>
                    <tr class="table-info">
                        <!-- <th>Select All <input type='checkbox' id='selectAll'></th> -->
                        <th>ID</th>
                        <th>Mode</th>
                        <th>AWB No.</th>
                        <th>Forwader</th>
                        <th>Shipped By</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id='checkTable'>
                </tbody>
            </table>
        </div>


    </div>
</div>
@stop



@section('js')
<script type="text/javascript">

</script>
@stop