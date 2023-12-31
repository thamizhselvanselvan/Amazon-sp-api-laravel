@extends('adminlte::page')

@section('title', 'Edit Dispose Reason')

@section('css')

    <link rel="stylesheet" href="/css/styles.css">

@stop

@section('content_header')

    <div class="row">
        <div class="col">
            <a href="{{ route('disposes.index') }}" class="btn btn-primary">

                <i class="fas fa-long-arrow-alt-left"></i> Back
            </a>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col">
            <h1 class="m-0 text-dark text-center ">Edit Dispose Reason</h1>
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

            @if (session()->has('success'))
                <div class="alert alert-success">
                    {{ session()->get('success') }}
                </div>
            @endif

            @if (session()->has('error'))
                <div class="alert alert-danger">
                    {{ session()->get('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('disposes.update', $name->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row justify-content-center">
                    <div class=" col-9">
                        {{-- <x-adminlte-input label="Dispoe Reason" name="reason" value="{{ $name->reason }}" type="text" /> --}}
                        <x-adminlte-select label="Select Reason" name="reason" value="{{ $name->reason }}" type="text">
                            <option>Select Reason</option>
                            <option>Products that have expired</option>
                            <option>Defective products</option>
                            <option>Product Missmatch</option>
                            <option>Something </option>
                           
                            
                    </div>

                </div>
                </x-adminlte-select>
                <div class="col-3"></div>

                <div class="col-12 text-center">
                    <x-adminlte-button label="Submit" theme="primary" class="destination.update" icon="fas fa-save"
                        type="submit" />
                </div>

            </form>

        </div>
        <div class="col"></div>
    </div>

@stop
