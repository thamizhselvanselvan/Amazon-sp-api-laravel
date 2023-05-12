@extends('adminlte::page')

@section('title', 'Add AWB No.')

@section('content_header')
    <div class="row">
        <h1 class="m-0 text-dark col">Upload AWB No.</h1>
        <h2 class="mb-4 text-right col">
            <a href="{{ route('shipntrack.smsa.upload') }}">
                <x-adminlte-button label="Back" theme="primary" icon="fas fa-long-arrow-alt-left" class="btn-sm" />
            </a>
        </h2>
    </div>
@stop

@section('content')

    <div class="row m-3 ">
        <div class="col-12 text-box-input">
            <form class="row" action="{{ Route('shipntrack.smsa.gettracking') }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <label>Enter AWB No.</label>
                <textarea class="form-control" rows="3" placeholder="Tracking Number" name="smsa_awbNo"></textarea>
                <div class="text-right m-2">
                    <x-adminlte-button label='Submit' class="btn-sm" theme="primary" icon="fas fa-file-upload"
                        type="submit" />
                </div>
            </form>
        </div>
    </div>

@stop

@section('js')
    <script></script>
@stop
