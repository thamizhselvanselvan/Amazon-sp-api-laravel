@extends('adminlte::page')

@section('title', 'Event Master')
@section('css')
<style>


</style>
@stop

@section('content_header')

<div class="row">
    <h1 class="mb-2 text-dark col">Tracking Event Master</h1>
    <!-- <a href="{{Route('shipntrack.trackingEvent.upload')}}">
        <x-adminlte-button label="Add New Records" theme="primary" icon="fas fa-file-upload" class="btn-sm" />
    </a>
    <a href="#{{Route('shipntrack.forwarder.template')}}">
        <x-adminlte-button label="Download Templates" theme="primary" icon="fas fa-file-download" class="btn-sm" />
    </a> -->
</div>

<div class="card ">
    <!-- <h3 class="card-header text-center">{{ (isset($records)) ? 'Update Event' : 'Add Event' }}</h3> -->
    <form class="ml-4 mt-1 mr-4"
        action="{{(isset($records)) ? Route('shipntrack.eventMaster.update', $records->id) : Route('shipntrack.trackingEvent.save')}}"
        method="POST">
        @csrf
        <div class="row">

            <div class="col">
                <x-adminlte-input label="Event Code" name="event_code" type="text" placeholder="Event Code"
                    value="{{ (isset($records->event_code)) ? $records->event_code : '' }}" />

                <div class="form-group mt-0">
                    <label for="Active">Active</label>
                    @if ((isset($records)) && $records->active == 1)
                    <input type="checkbox" name="event_check" checked>
                    @else
                    <input type="checkbox" name="event_check">
                    @endif
                </div>
            </div>
            <div class="col">
                <x-adminlte-textarea label="Event Description" name="event_desc" type="text"
                    placeholder="Event Description">{{ (isset($records->description)) ? $records->description : '' }}
                </x-adminlte-textarea>
            </div>
        </div>
        <h2 class="text-left col">
            @if ((isset($records)))

            <a href="{{ route('shipntrack.trackingEvent.back') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            @endif

            <x-adminlte-button label="{{ (isset($records)) ? 'Update' : 'Save' }}" name="btn" type="submit"
                theme="{{ (isset($records)) ? 'primary' : 'success' }}"
                icon="{{ (isset($records)) ? 'fas fa-edit' : 'fas fa-check-circle' }}" class="btn-sm" />
        </h2>
    </form>

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
                <!-- <th>ID</th> -->
                <th>Event Code</th>
                <th>Description</th>
                <th>IsActive</th>
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
        columns: [
            // {
            //     // data: 'id',
            //     // name: 'id',
            //     orderable: false,
            //     searchable: false
            // },
            {
                data: 'event_code',
                name: 'event_code',
            },
            {
                data: 'description',
                name: 'description',
            },
            {
                data: 'status',
                name: 'status',
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            },

        ],
    });
});

$(document).on('click', '.delete', function() {
    let bool = confirm('Are you sure you want to delete?');
    if (!bool) {
        return false;
    }
})
</script>
@stop
