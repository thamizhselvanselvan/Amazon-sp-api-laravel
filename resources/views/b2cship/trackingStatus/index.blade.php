@extends('adminlte::page')
@section('title', 'Status Details')

@section('content_header')
<div class="row">
    <h1 class="m-0 text-dark"> B2CShip Tracking Status Details</h1>
    <div class="col text-right">
        <a href="csv_export">
            <x-adminlte-button label="Export To CSV" theme="primary" icon="fas fa-file-export" class="btn-sm" />
        </a>
    </div>
</div>

@stop

@section('css')
<style>
    .table td {
        padding: 0.1rem;
    }
</style>
@stop

@section('content')


<table class="table table-bordered yajra-datatable table-striped" style="font-size:13px;">

    <thead>
        <tr>
            <!-- <th>S/N</th> -->
            <th>Tracking Message</th>
            <th>Tracking Master Code</th>
            <th>Tracking Master Event Description</th>
            <th>Our Event Code</th>
            <th>Event Description</th>
            <th>Tracking API Event</th>
            <th>Micro Status</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>

@stop

@section('js')
<script type="text/javascript">
    $(function() {

        let data = [];
        let data_table = $('.yajra-datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "/b2cship/tracking_status/details",
            pageLength: 500,
            lengthMenu: [50, 100, 200, 500],
            columns: [{
                    data: 'TrackingMsg'
                },
                {
                    data: 'TrackingMasterCode'
                },
                {
                    data: 'TrackingMasterEventDescription'
                },
                {
                    data: 'OurEventCode'
                },
                {
                    data: 'EventDescription'
                },
                {
                    data: 'TrackingAPIEvent'
                },
                {
                    data: 'MicroStatus'
                }
            ],
        });
    });
</script>

@stop