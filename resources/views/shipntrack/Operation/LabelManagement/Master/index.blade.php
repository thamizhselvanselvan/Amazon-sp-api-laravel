@extends('adminlte::page')
@section('title', 'Label Master')

@section('css')

@stop

@section('content-header')

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
    <div class="row">
        <div class="col"></div>
        <div class="col">
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="text-center mb-4 font-weight-bold">ShipNTrack Label Master</h5>

                </div>
                <div class="card-body">
                    <form action="{{ route('shipntrack.label.master.submit') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <x-adminlte-input label="Source" name="source" igroup-size="md" type="text" placeholder="Source"
                            fgroup-class="col-md-12" maxlength="3" id="source" autocomplete="off" required />

                        <x-adminlte-input label="Destination" name="destination" igroup-size="md" type="text"
                            placeholder="Destination" fgroup-class="col-md-12" id="destination" autocomplete="off"
                            required />

                        <x-adminlte-input label="Upload Logo" type="file" name="logo" igroup-size="md"
                            placeholder="Choose a file..." fgroup-class="col-md-12" required>
                            <x-slot name="prependSlot">
                                <div class="input-group-text bg-lightblue">
                                    <i class="fas fa-upload"></i>
                                </div>
                            </x-slot>
                        </x-adminlte-input>

                        <x-adminlte-textarea label="Return Address" name="return_address" igroup-size="md"
                            placeholder="Enter Return Address" fgroup-class="col-md-12" autocomplete="off" required />

                        <x-adminlte-button class="btn-sm float-right mr-2" type="submit" label="Submit" theme="success"
                            icon="fas fa-lg fa-save" />

                    </form>
                </div>
            </div>
        </div>
        <div class="col"></div>
    </div>

@stop

@section('js')
    <script>
        $('#source').keyup(function() {
            this.value = this.value.toUpperCase();
        });

        $('#destination').keyup(function() {
            this.value = this.value.toUpperCase();
        });
    </script>
@stop
