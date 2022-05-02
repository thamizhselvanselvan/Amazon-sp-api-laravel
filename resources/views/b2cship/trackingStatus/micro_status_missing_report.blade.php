@extends('adminlte::page')
@section('title', 'Micro Status Missing Report')

@section('content_header')
<h1 class="m-0 text-dark"> B2CShip Micro Status Missing Report</h1>
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
            <th>S/N</th>
            <th>Tracking Status</th>
            <th>Tracking Message</th>
            <!-- <th>Tracking Master Event Description</th>
            <th>Our Event Code</th>
            <th>Event Description</th>
            <th>Tracking API Event</th>
            <th>Micro Status</th> -->
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
            ajax: "/b2cship/micro_status_missing_report",
            pageLength: 500,
            lengthMenu: [50, 100, 200, 500],
            columns: [
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'Status'
                },
                {
                    data: 'Tracking_msg'
                },

            ],
        });
    });
</script>

@stop