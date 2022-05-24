@extends('adminlte::page')

@section('title', 'Edit Asin')

@section('css')

    <link rel="stylesheet" href="/css/styles.css">

@stop

@section('content_header')

    <div class="row">
        <div class="col">
            <a href="{{ Route('asin-master') }}" class="btn btn-primary">

                <i class="fas fa-long-arrow-alt-left"></i> Back
            </a>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col">
            <h1 class="m-0 text-dark text-center ">Edit Asin</h1>
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

            @if(session()->has('error'))
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

            <form class="row" action="{{Route('asin.update', $asin->id) }}" method="POST" id="">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-6">
                        {{-- <x-adminlte-input label="Asin" name="asin" id="asin" type="text" placeholder="Asin"  />
                <x-adminlte-input label="Country Code" name="country_code" id="country_code" type="text" placeholder="Country Code" value="{{ $asin->country_code }}" /> --}}

                        <x-adminlte-input label="Asin" name="asin" id="" value="{{ $asin->asin }}"  type="text" placeholder="Asin" />
                    </div>
                    <div class="col-6">
                        <x-adminlte-input label="source" name="source" id=""  value="{{ $asin->source }}" type="text" placeholder="Source" />
                    </div>

                    <div class="col-6">
                            <x-adminlte-input label="destination 1" name="destination_1" id="" value="{{ $asin->destination_1 }}" type="text"
                            placeholder="Destination 1" />
                           
                    </div>

                    <div class="col-6">
                        <x-adminlte-input label="Destination 2" name="destination_2" id="" value="{{ $asin->destination_2 }}" type="text"
                            placeholder="Destination 2" />
                    </div>
                    <div class="col-6">
                        <x-adminlte-input label="Destination 3" name="destination_3" id="" value="{{ $asin->destination_3 }}" type="text"
                            placeholder="Destination 3" />
                    </div>
                    <div class="col-6">
                        <x-adminlte-input label="Destination 4" name="destination_4" id=""  value="{{ $asin->destination_4 }}" type="text"
                            placeholder="Destination 4" />
                    </div>
                    <div class="col-6">
                        <x-adminlte-input label="Destination 5" name="destination_5" id=""  value="{{ $asin->destination_5 }}" type="text"
                            placeholder="Destination 5" />
                    </div>

                </div>
        </div>
        <div class="col-3"></div>

        <div class="col-12 text-center">
            <x-adminlte-button label="Edit Asin" theme="primary" class="asin.update" icon="fas fa-save" type="submit" />
        </div>
        </form>
    </div>
    <div class="col"></div>
    </div>

@stop
