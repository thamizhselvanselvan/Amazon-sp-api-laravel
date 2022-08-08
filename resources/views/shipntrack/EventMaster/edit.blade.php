@extends('adminlte::page')

@section('title', 'Event Master Edit')

@section('content_header')
<div class="row">
    <h1 class="m-0 text-dark col">Tracking Event Master</h1>
</div>
<div class="row mt-4">
    <div class="col">
        <a href="{{ route('shipntrack.trackingEvent.back') }}" class="btn btn-primary">

            <i class="fas fa-long-arrow-alt-left"></i> Back
        </a>
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
        </div>
    </div>
</div>
<form action="{{ route('shipntrack.eventMaster.update', $records->id) }}" method="POST">
    @csrf

    <div class="row justify-content-center">
        <div class="col-4"></div>
        <div class="col-4">
            <h2 class="text-center text-success">Update</h2>
            <x-adminlte-input label="Event Code" name="event_code" type="text" placeholder="Event Code "
                value=" {{$records->event_code}} " />
            <x-adminlte-textarea label="Description" name="description" id="description" type="text"
                placeholder="Description " />
            <x-adminlte-select label="Status" name="status" value="{{$records->active}}">
                <!-- <option value=""> Select an option </option> -->
                @if ($records->active == 'TRUE')
                <option value="FALSE"> FALSE </option>
                <option value="{{$records->active}}" selected> {{$records->active}} </option>
                @else
                <option value="{{$records->active}}" selected> {{$records->active}} </option>
                <option value="TRUE"> TRUE </option>
                @endif
            </x-adminlte-select>
            <!-- <x-adminlte-button label="Update" theme="success" icon="fas fa-edit" class="float-right" /> -->
            <input type="submit" class="btn btn-success float-right" value="Update" />
        </div>
        <div class="col-4">
        </div>
    </div>

</form>
@stop

@section('js')
<script>
var value = document.getElementById('description');
value.innerHTML = '{{$records->description}}';
</script>
@stop
