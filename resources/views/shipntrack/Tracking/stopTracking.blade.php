@extends('adminlte::page')

@section('title', 'Stop Tracking')

@section('content_header')
<div class="container">
    <h1 class="m-0 text-dark col">Stop Packet Tracking</h1>
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
<div class="container">
    <div class="pl-2 row ">
        <div class="col">
            <x-adminlte-select name="forwarder" id='forwarder' label="Select Courier Partner">

                <option value="select">--Select--</option>
                @foreach ($courier_partner as $name )
                <option value="{{$name}}">{{$name}}</option>
                @endforeach
            </x-adminlte-select>
        </div>

        <div class="col">
            <x-adminlte-select name="status" id='status' label="Tracking Status">

            </x-adminlte-select>
        </div>
    </div>
    <div class="row">
        <div class="text-right m-3 ">
            <x-adminlte-button label='Submit' class="btn-sm" theme="primary" icon="fas fa-file-upload" type="submit" />
        </div>
    </div>
</div>
@stop

@section('js')
<script type="text/javascript">

</script>
@stop