@extends('adminlte::page')

@section('title', 'Edit Shelves')

@section('css')

    <link rel="stylesheet" href="/css/styles.css">

@stop

@section('content_header')

    <div class="row">
        <div class="col">
            <a href="{{ Route('shelves.index') }}" class="btn btn-primary">

                <i class="fas fa-long-arrow-alt-left"></i> Back
            </a>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col">
            <h1 class="m-0 text-dark text-center ">Edit Shelves</h1>
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

            <form  action="{{ route('shelves.update', $shelve->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row justify-content-center">

                    <div class="col-6">

                        <x-adminlte-select name="rack_id" label="Select Rack">

                            @foreach ($rack_lists as $rack_list)
               
                                @if ($rack_list->id == $shelve->rack_id)
                                    <option value="{{ $rack_list->id }}" selected>{{ $rack_list->name  }}</option>
                                @else
                                    <option value="{{ $rack_list->id }}">{{ $rack_list->name  }}</option>
                                @endif

                            @endforeach

                        </x-adminlte-select>

                    </div>
            
                </div>

                <div class="row justify-content-center">

                    <div class=" col-6">
                        <x-adminlte-input label="Shelve Name" name="name" value="{{ $shelve->name }}" type="text" placeholder="name" />
                    </div>
                
                </div>    

                <div class="col-3"></div>

                <div class="col-12 text-center">
                    <x-adminlte-button label="Submit" theme="primary" class="Shelves.update" icon="fas fa-save" type="submit" />
                </div>
            </form>
        </div>

        <div class="col"></div>
    </div>

@stop
