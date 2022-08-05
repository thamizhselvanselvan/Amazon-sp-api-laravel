@extends('adminlte::page')

@section('title', 'Upload')

@section('content_header')

<div class="row">
    <div class="col">
        <a href="{{route('shipntrack.trackingEvent.back')}}" class="btn btn-primary btn-sm">
            <i class="fas fa-long-arrow-alt-left"></i> Back
        </a>
    </div>
</div>

<div class="row mt-3">
    <div class="col">
        <h1 class="m-0 text-dark text-center ">Upload Tracking Event CSV.</h1>
    </div>
</div>

@stop


@section('content')

<div class="loader d-none">
    <div class="sub-loader position-relative ">
        <div class="lds-hourglass"></div>
        <p>Loading...</p>
    </div>
</div>

<div class="row">
    <div class="col"></div>
    <div class="col-8">

        @if(session()->has('success'))
        <x-adminlte-alert theme="success" title="Success" dismissable>
            {{ session()->get('success') }}
        </x-adminlte-alert>
        @endif

        @if(session()->has('error'))
        <x-adminlte-alert theme="danger" title="Error" dismissable>
            {{ session()->get('error') }}
        </x-adminlte-alert>
        @endif

        <form class="row" action="{{ route('shipntrack.eventMaster.filesave')}}" method="POST"
            enctype="multipart/form-data">
            @csrf

            <div class="col-3"></div>
            <div class="col-6">
                <div class="row">
                    <div>
                        <x-adminlte-select label="Courier partner" name="courier_partner" class="courier_partner">
                            <option value=""> Select a courier partner </option>
                            <option value="master"> Master </option>
                            <option value="bombino"> Bombino </option>
                            <option value="samsa"> SAMSA </option>
                            <option value="emirate"> Emirate </option>
                        </x-adminlte-select>

                        <x-adminlte-input class="ml-1" label="Choose csv file" name="tracking_event_csv"
                            id="tracking_event_csv_file" type="file" />
                    </div>
                </div>
            </div>
            <div class="col-3"></div>
            <div class="col-12">
                <div class="text-center">
                    <x-adminlte-button label="Submit" theme="primary" class="eventMaster btn-sm" icon="fas fa-plus"
                        type="submit" />
                </div>
            </div>
        </form>
    </div>
    <div class="col"></div>
</div>

@stop

@section('js')
<script>
$('.eventMaster').click(function() {
    var option = $('.courier_partner').val();
    // alert(option);
});
</script>
@stop
