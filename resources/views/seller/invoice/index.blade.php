@extends('adminlte::page')

@section('title', 'Seller Invoice')

@section('css')
    <!-- <link rel="stylesheet" href="/css/styles.css"> -->
@stop

@section('content_header')
    <h1 class="m-0 text-dark">Seller Invoice Management</h1>
@stop


@section('content')

    <div class="loader d-none">
        <div class="sub-loader position-relative ">
            <div class="lds-hourglass"></div>
            <p>Loading...</p>
        </div>
    </div>

    <div class="row text-center mt-3 mb-5">
        <div class="col"></div>

        <div class="col-4 text-left">

            @if ($message = Session::get('success'))
                <div class="alert alert-success alert-block">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <strong>{{ $message }}</strong>
                </div>
            @endif

            @if ($message = Session::get('error'))
                <div class="alert alert-danger alert-block">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <strong>{{ $message }}</strong>
                </div>
            @endif
        </div>
    </div>

@stop




