@extends('adminlte::page')

@section('title', 'Edit Rack')

@section('css')

<link rel="stylesheet" href="/css/styles.css">

@stop

@section('content_header')

<div class="row">
    <div class="col">
        <a href="{{ route('racks.index') }}" class="btn btn-primary">

            <i class="fas fa-long-arrow-alt-left"></i> Back
        </a>
    </div>
</div>

<div class="row mt-3">
    <div class="col">
        <h1 class="m-0 text-dark text-center ">Edit Rack</h1>
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

        <form action="{{ route('racks.update', $name->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row justify-content-center">
                <div class="col-4">
                    <x-adminlte-select name="warehouse_id" label="Select Warehouse">
                        @foreach ($warehouse_lists as $warehouse_list)
                        @if ($warehouse_list->id == $selected_warehouse)
                        <option value="{{ $warehouse_list->id }}" selected> {{ $warehouse_list->name }}</option>
                        @else
                        <option value="{{ $warehouse_list->id }}">{{$warehouse_list->name }}</option>
                        @endif
                        @endforeach
                    </x-adminlte-select>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-4">
                    <x-adminlte-input label="Rack ID" name="rack_id" type="text" value="{{  $name->rack_id }}" />
                </div>
            </div>
            <div class="row justify-content-center">
                <div class=" col-4">
                    <x-adminlte-input label="Name" name="name" value="{{ $name->name }}" type="text" />
                </div>
            </div>
            <div class="col-3"></div>
            <div class="col-12 text-center">
                <x-adminlte-button label="Submit" theme="primary" class="rack.update" icon="fas fa-save" type="submit" />
            </div>
        </form>
    </div>
    <div class="col"></div>
</div>

@stop