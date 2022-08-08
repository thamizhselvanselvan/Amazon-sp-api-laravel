@extends('adminlte::page')

@section('title', 'Event Master')
@section('css')
<style>


</style>
@stop

@section('content_header')
<div class="row">
    <h1 class="m-0 text-dark col">Tracking Event Master</h1>
    <h2 class="mb-4 text-right col">
        <a href="{{Route('shipntrack.trackingEvent.upload')}}">
            <x-adminlte-button label="Add New Records" theme="primary" icon="fas fa-file-upload" class="btn-sm" />
        </a>
        <a href="#{{Route('shipntrack.forwarder.template')}}">
            <x-adminlte-button label="Download Templates" theme="primary" icon="fas fa-file-download" class="btn-sm" />
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
<div class="pl-2">
    <table class="table table-bordered yajra-datatable table-striped table-sm TrackingEventMaster">
        <thead class="bg-info">
            <tr>
                <th>ID</th>
                <th>Event Code</th>
                <th>Description</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
@stop

@section('js')
<script>
$(function() {

    let yajra_table = $('.yajra-datatable').DataTable({

        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ url('/shipntrack/event-master') }}",

        },
        pageLength: 200,
        columns: [{
                data: 'id',
                name: 'id',
                orderable: false,
                searchable: false
            },
            {
                data: 'event_code',
                name: 'event_code',
            },
            {
                data: 'description',
                name: 'description',
            },
            {
                data: 'action',
                name: 'action',
            },

        ],
    });
});
</script>
@stop
