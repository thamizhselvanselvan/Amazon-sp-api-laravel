@extends('adminlte::page')

@section('title', 'Edit Vendor')

@section('css')

    <link rel="stylesheet" href="/css/styles.css">

@stop

@section('content_header')

    <div class="row">
        <div class="col">
            <a href="{{ route('vendors.index') }}" class="btn btn-primary">

                <i class="fas fa-long-arrow-alt-left"></i> Back
            </a>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col">
            <h1 class="m-0 text-dark text-center ">Edit vendor</h1>
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

        <form  action="{{ route('vendors.update', $name->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row justify-content-center">
                <div class="col-6">
                        <x-adminlte-input label="Name" name="name" value="{{ $name->name }}" type="text"  />
                </div>
                <div class="col-6">
                    <x-adminlte-select name="type" label="Select type" value="{{ $name->type  }}">
                            <option>Select Type</option>
                            <option>Source</option>
                            <option>Destination</option>
                        </x-adminlte-select>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-6">
                    <x-adminlte-input label="Country" name="country"   value="{{ $name->country  }}" />
                </div>
                <div class="col-6">
                    <x-adminlte-input label="Currency" name="currency" type="text" value="{{  $name->currency }}" />
                </div>
            </div>


         
            <!-- <x-adminlte-input label="Name" name="name" value="{{ $name->name }}" type="text"  /> -->
            <div class="col-3"></div>

            <div class="col-12 text-center">
                <x-adminlte-button label="Submit" theme="primary" class="rack.update" icon="fas fa-save" type="submit" />
            </div>

        </form>

    </div>
    <div class="col"></div>
</div>

@stop
