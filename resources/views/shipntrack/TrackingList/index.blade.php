@extends('adminlte::page')

@section('title', 'Tracking List')
@section('css')
<style>


</style>
@stop

@section('content_header')
<div class="row">
    <h1 class="mb-2 text-dark col">Tracking List</h1>
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

        <div class="alert_display">
            @if ($message = Session::get('error'))
            <div class="alert alert-danger alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $message }}</strong>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- <div class="container-"> -->
<div class="card">
    <div class="card-header">
        <div class="row ">
            <div class="col"></div>
            <div class="col"></div>
            <div class="col ">
                <x-adminlte-input label="AWB No." type="text" name="awbno" placeholder="Search by awb no" id="awbNo">
                    <x-slot name="appendSlot">
                        <x-adminlte-button theme="outline-success" label="Search" theme="success" class="search-btn" />
                    </x-slot>
                    <x-slot name="prependSlot">
                        <div class="input-group-text text-success">
                            <i class="fas fa-search"></i>
                        </div>
                    </x-slot>
                </x-adminlte-input>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col">
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>AWB No.:</th>
                            <th> </th>
                            <th>Booking Date :</th>
                            <th> </th>
                        </tr>
                        <tr>
                            <th>No. of PCS.:</th>
                            <th> </th>
                            <th>Billing Weight:</th>
                            <th> </th>
                        </tr>
                        <tr>
                            <th>Forwarder:</th>
                            <th> </th>
                            <th>Forwarder 1 :</th>
                            <th> </th>
                        </tr>
                        <tr>
                            <th>Forwarder No.:</th>
                            <th> </th>
                            <th>Forwarder No.1 :</th>
                            <th> </th>
                        </tr>
                        <tr>
                            <th>Vendor:</th>
                            <th> </th>
                            <th>Payment Type :</th>
                            <th> </th>
                        </tr>
                        <tr>
                            <th>Client Name:</th>
                            <th> </th>
                            <th> </th>
                            <th> </th>
                        </tr>
                        <tr>
                            <th>Batch:</th>
                            <th></th>
                            <th>Packet Status</th>
                            <th> </th>
                        </tr>
                    </thead>

                </table>
            </div>
        </div>
    </div>
    <div class="pl-2">
        <table class="table table-bordered yajra-datatable table-striped table-sm ">
            <thead class="bg-info">
                <tr>
                    <th>Status Date</th>
                    <th>Status Time</th>
                    <th>Location</th>
                    <th>Status Details</th>
                    <th>Status Source</th>
                    <th>Created Source</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>
<!-- </div> -->
@stop

@section('js')
<script>
$(function() {


    let yajra_table = $('.yajra-datatable').DataTable({
        processing: true,
        serverSide: true,
        orderable: false,
        searchable: false,
        ajax: {
            url: "{{ url('/shipntrack/trackingList/search') }}",
            data: {},
        },
        pageLength: 200,
        columns: [{
                data: 'Date_Time',
                name: 'Date_Time',
                orderable: false,
                searchable: false
            },
            // {
            //     data: 'our_event_description',
            //     name: 'our_event_description',
            // },
            // {
            //     data: 'master_event_code',
            //     name: 'master_event_code',
            // },
            // {
            //     data: 'master_description',
            //     name: 'master_description',
            // },
            // {
            //     data: 'our_event_code',
            //     name: 'our_event_code',
            // },
            // {
            //     data: 'status',
            //     name: 'status',
            // },
            // {
            //     data: 'action',
            //     name: 'action',
            //     orderable: false,
            //     searchable: false
            // },

        ],
    });
});

$('.search-btn').on('click', function() {
    let awbNo = $('#awbNo').val();
    // alert(awbNo);

    $.ajax({
        url: "{{ url('/shipntrack/trackingList/search') }}",
        method: "GET",
        data: {
            "awbNo": awbNo,
            "_token": "{{ csrf_token() }}",
        },
        success: function(result) {

        },
    });
});
</script>
@stop
