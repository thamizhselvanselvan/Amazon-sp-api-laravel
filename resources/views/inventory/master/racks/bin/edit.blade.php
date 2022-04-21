@extends('adminlte::page')

@section('title', 'Edit Bin')

@section('css')

    <link rel="stylesheet" href="/css/styles.css">

@stop

@section('content_header')

    <div class="row">
        <div class="col">
            <a href="{{ Route('inventory.bin_index') }}" class="btn btn-primary">

                <i class="fas fa-long-arrow-alt-left"></i> Back
            </a>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col">
            <h1 class="m-0 text-dark text-center ">Edit Bin</h1>
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

            <form  action="{{Route('inv.bin_update', $name->id) }}" method="POST" id="">
                @csrf
                @method('PUT')

                <div class="row">
            
                    <div class=" col-6">
                        <x-adminlte-input label="Depth" name="Depth" id=""  value="{{ $name->Depth }}" type="text" placeholder="Depth" />
                    </div>
                    
                    <div class=" col-6">
                        <x-adminlte-input label="Width" name="Width" id=""  value="{{ $name->Width }}" type="text" placeholder="Width" />
                    </div>
                    
                    <div class=" col-6">
                        <x-adminlte-input label="Hight" name="Hight" id=""  value="{{ $name->Hight }}" type="text" placeholder="Height" />
                    </div>
                    
                    <div class=" col-6">
                        <x-adminlte-input label="Zone" name="Zone" id=""  value="{{ $name->Zone }}" type="text" placeholder="Zone" />
                    </div>
                    

                </div>
        </div>
        <div class="col-3"></div>

        <div class="col-12 text-center">
            <x-adminlte-button label="Edit Bin" theme="primary" class="Bin.update" icon="fas fa-save" type="submit" />
        </div>
        </form>
    </div>
    <div class="col"></div>
    </div>

@stop
