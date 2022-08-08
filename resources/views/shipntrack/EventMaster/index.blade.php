@extends('adminlte::page')

@section('title', 'Event Master')
@section('css')
<style>


</style>
@stop

@section('content_header')

<div class="row">
    <h1 class="m-0 text-dark col">Tracking Event Master</h1>
    <!-- <a href="{{Route('shipntrack.trackingEvent.upload')}}">
        <x-adminlte-button label="Add New Records" theme="primary" icon="fas fa-file-upload" class="btn-sm" />
    </a>
    <a href="#{{Route('shipntrack.forwarder.template')}}">
        <x-adminlte-button label="Download Templates" theme="primary" icon="fas fa-file-download" class="btn-sm" />
    </a> -->
</div><br>

@if ($records != '')
<form action="{{ route('shipntrack.eventMaster.update', $records->id) }}" method="POST">
    @csrf
    <div class="row">
        <div class="col">
            <div>
                <x-adminlte-select label="Courier partner" name="courier_partner" class="courier_partner">
                    <option value=""> Select a courier partner </option>
                    <option value="master"> Master </option>
                    <option value="bombino"> Bombino </option>
                    <option value="samsa"> SAMSA </option>
                    <option value="emirate"> Emirate </option>
                </x-adminlte-select>

                <div class="form-group ">
                    <label for=" Active">Active</label>
                    @if ($records->active == 1)
                    <input type="checkbox" name="event_check" checked>
                    @else
                    <input type="checkbox" name="event_check">
                    @endif

                </div>
            </div>
        </div>
        <div class="col">

            <x-adminlte-input label="Event Code" name="event_code" type="text" placeholder="Event Code"
                value="{{$records->event_code}}" />
        </div>
        <div class="col">
            <x-adminlte-textarea label="Event Description" name="event_desc" type="text"
                placeholder="Event Description"> {{$records->description}}
            </x-adminlte-textarea>
        </div>

    </div>

    <h2 class="mb-4 text-left col">
        <x-adminlte-button label="Update" name="btn" type="submit" theme="primary" icon="fas fa-edit" class="btn-sm" />
    </h2>

</form>
@else

<form action="{{Route('shipntrack.trackingEvent.save')}}" method="POST">
    @csrf
    <div class="row">
        <div class="col">
            <div>
                <x-adminlte-select label="Courier partner" name="courier_partner" class="courier_partner">
                    <option value=""> Select a courier partner </option>
                    <option value="master"> Master </option>
                    <option value="bombino"> Bombino </option>
                    <option value="samsa"> SAMSA </option>
                    <option value="emirate"> Emirate </option>
                </x-adminlte-select>

                <div class="form-group mt-0">
                    <label for="Active">Active</label>
                    <input type="checkbox" name="event_check">
                </div>

            </div>
        </div>
        <div class="col">
            <x-adminlte-input label="Event Code" name="event_code" type="text" placeholder="Event Code" />
        </div>
        <div class="col">
            <x-adminlte-textarea label="Event Description" name="event_desc" type="text"
                placeholder="Event Description" />
        </div>
    </div>

    <h2 class="mb-4 text-left col">
        <x-adminlte-button label="Save" name="btn" type="submit" theme="success" icon="fas fa-check-circle"
            class="btn-sm" />
    </h2>
</form>

@endif
<hr>


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

$(document).on('click', '.delete', function() {
    let bool = confirm('Are you sure you want to delete?');
    if (!bool) {
        return false;
    }
})
</script>
@stop
