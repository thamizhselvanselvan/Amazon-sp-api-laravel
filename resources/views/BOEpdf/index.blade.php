@extends('adminlte::page')

@section('title', 'BOE Master')

@section('content_header')
    <h1 class="m-0 text-dark">Bill OF Entry</h1>
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
            <h2 class="mb-4">


                <a href="uplod">
                    <x-adminlte-button label=" Pdf Upload" theme="primary" icon="fas fa-file-export" />
                </a>

            </h2>


        @stop
