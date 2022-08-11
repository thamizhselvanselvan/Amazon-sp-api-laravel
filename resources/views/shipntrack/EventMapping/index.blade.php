@extends('adminlte::page')

@section('title', 'Event Mapping')
@section('css')
<style>


</style>
@stop

@section('content_header')
<div class="row">
    <h1 class="mb-2 text-dark col">Tracking Event Mapping</h1>
</div>

<div class="card ">
    <!-- <h3 class="card-header text-center">{{ (isset($records)) ? 'Update Event' : 'Add Event' }}</h3> -->
    <form class="ml-4 mt-1 mr-4" action="{{ Route('shipntrack.EventMapping.save')}}" method="POST">
        @csrf
        <div class="row">

            <div class="col">
                <x-adminlte-select name="source" label="Select Source" id="source">
                    <x-adminlte-options :options="['Bombino', 'Samsa', 'Emirate']" empty-option="Select Source" />

                </x-adminlte-select>

            </div>
            <div class="col">

                <x-adminlte-input label="Event Code" name="our_event_code" type="text" id="event_code"
                    placeholder="Event Code" value="" />

            </div>
            <div class="col">

                <x-adminlte-select name="event_source" label="Master Event Source" id="master_event_desc">
                    <option value="">Select Master Event Source</option>
                    @foreach ($master_record as $record)
                    <option value="{{$record->event_code}}">{{$record->event_code}} : {{$record->description}}</option>
                    @endforeach
                </x-adminlte-select>

            </div>
            <div class="col">

                <x-adminlte-select name="event_description" label="Event Description" id="event_desc">
                    <option value="">Select Event Description</option>
                </x-adminlte-select>

            </div>
        </div>

        <div class="form-group mt-0">
            <label for="Active">Active</label>
            <!-- @if ((isset($records)) && $records->active == 1) -->
            <input type="checkbox" name="event_check" checked>
            <!-- @else -->
            <input type="checkbox" name="event_check">
            <!-- @endif -->
        </div>

        <div class="mb-1 text-left col">
            @if ((isset($records)))
            <a href="{{ route('shipntrack.trackingEvent.back') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            @endif

            <x-adminlte-button label="{{ (isset($records)) ? 'Update' : 'Save' }}" name="btn" type="submit"
                theme="{{ (isset($records)) ? 'primary' : 'success' }}"
                icon="{{ (isset($records)) ? 'fas fa-edit' : 'fas fa-check-circle' }}" class="btn-sm" />
        </div>
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
    <table class="table table-bordered yajra-datatable table-striped table-sm ">
        <thead class="bg-info">
            <tr>
                <th>Source</th>
                <th>Our Event Code</th>
                <th>Our Event Description</th>
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
            url: "{{ url('/shipntrack/event-mapping') }}",
            data: {

            },
        },
        pageLength: 200,
        columns: [{
                data: 'source',
                name: 'source',
                orderable: false,
                searchable: false
            },
            {
                data: 'our_event_code',
                name: 'our_event_code',
            },
            {
                data: 'our_event_description',
                name: 'our_event_description',
            },
            {
                data: 'master_event_code',
                name: 'master_event_code',
            },
            {
                data: 'master_description',
                name: 'master_description',
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

$('#source').change(function() {
    var source = $(this).val();
    // alert(source);
    $.ajax({
        url: "{{ url('/shipntrack/event-mapping/source') }}",
        method: "POST",
        data: {
            "source": source,
            "_token": "{{ csrf_token() }}",
        },
        success: function(result) {
            console.log(result);
            let records = '';
            $.each(result, function(index, result) {
                records += "<option value='" + result + "'>" + result + "</option>"
            });
            $('#event_desc').append(records);
        }
    });
});

$('#master_event_desc').change(function() {
    let event_code = $(this).val();
    document.getElementById('event_code').value = 'PIL_' + event_code;

});
</script>
@stop
