@extends('adminlte::page')

@section('title', 'Event Master')

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

@stop

@section('js')

@stop
