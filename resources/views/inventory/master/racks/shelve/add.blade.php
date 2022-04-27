@extends('adminlte::page')

@section('title', 'Add Shelves')

@section('css')
    <link rel="stylesheet" href="/css/styles.css">
@stop

@section('content_header')

    <div class="row">
        <div class="col">
             <a href="{{ route('shelves.index') }}" class="btn btn-primary">  
                <i class="fas fa-long-arrow-alt-left"></i> Back
            </a>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col">
            <h1 class="m-0 text-dark text-center">Add Rack Shelves</h1>
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

             <form action="{{ route('shelves.store') }}" method="POST" id="admin_user">
                @csrf

                <div class="row justify-content-center">

                    <div class="col-6">

                        <x-adminlte-select name="rack_id" label="Select Rack">

                            @foreach ($rack_lists as $rack_list)
               
                                <option value="{{ $rack_list->id }}">{{ $rack_list->name  }}</option>

                            @endforeach

                        </x-adminlte-select>

                    </div>
                    
                </div>

                <div class="row justify-content-center">
                    <div class="col-6">
                        <x-adminlte-input label="Shelve Name" name="name" type="text" placeholder="Name"
                            value="{{ old('name') }}" />
                    </div>
                </div>

                <div class="text-center">
                    <x-adminlte-button label=" Submit" theme="primary" icon="fas fa-plus" type="submit" />
                </div>

            </form>
        </div>
        <div class="col"></div>
    </div>

@stop  