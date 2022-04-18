@extends('adminlte::page')

@section('title', 'Add Rack')

@section('css')
    <link rel="stylesheet" href="/css/styles.css">
@stop

@section('content_header')

    <div class="row">
        <div class="col">
             <a href="{{ route('inventory.rack_index') }}" class="btn btn-primary">  
                <i class="fas fa-long-arrow-alt-left"></i> Back
            </a>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col">
            <h1 class="m-0 text-dark text-center">Add Racks</h1>
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
        <div class="col-6">

            @if (session()->has('success'))
                <x-adminlte-alert theme="success" title="Success" dismissable>
                    {{ session()->get('success') }}
                </x-adminlte-alert>
            @endif

            @if (session()->has('error'))
                <x-adminlte-alert theme="danger" title="Error" dismissable>
                    {{ session()->get('error') }}
                </x-adminlte-alert>
            @endif

             <form action="{{ Route('admin_save_user') }}" method="POST" id="admin_user">


                @csrf

                <div class="row">
                    <div class="col-6">
                        <x-adminlte-input label="ID" name="ID" id="ID" type="text" placeholder="ID"
                            value="{{ old('ID') }}" />
                    </div>
                    <div class="col-6">
                        <x-adminlte-input label="Name" name="name" id="name" type="text" placeholder="Name"
                            value="{{ old('name') }}" />
                    </div>
                </div>


                <div class="text-center">
                    <x-adminlte-button label="Add Racks" theme="primary" icon="fas fa-plus" type="submit" />
                </div>
            </form>
        </div>
        <div class="col"></div>
    </div>

@stop  
