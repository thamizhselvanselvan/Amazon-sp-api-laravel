@extends('adminlte::page')

@section('title', 'Edit Bin')

@section('css')

<link rel="stylesheet" href="/css/styles.css">

@stop

@section('content_header')

<div class="row">
    <div class="col">
        <a href="{{ Route('bins.index') }}" class="btn btn-primary">

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

        <form action="{{ route('bins.update', $name->id) }}" method="POST" id="">
            @csrf
            @method('PUT')

            <div class="row justify-content-center">
                <div class="col-4">

                </div>
                <div class="col-4">
                    <x-adminlte-input label="Bin ID" name="bin_id" value="{{ $name->bin_id }}" type="text" placeholder="ID " />
                </div>
                <div class="col-4">
                    <x-adminlte-input label="Name" name="name" value="{{ $name->name }}" type="text" placeholder="Name " />
                </div>

            </div>
            <div class="row justify-content-center">
                <div class="col-4">

                </div>
                <div class=" col-4">
                    <x-adminlte-input label="Width" name="width" value="{{ $name->width }}" type="text" placeholder="Width" />
                </div>
                <div class=" col-4">
                    <x-adminlte-input label="Height" name="height" value="{{ $name->height }}" type="text" placeholder="Height" />
                </div>

                <div class=" col-4">
                    <x-adminlte-input label="Depth" name="depth" value="{{ $name->depth }}" type="text" placeholder="Depth" />
                </div>
            </div>
    </div>
    <div class="col-3"></div>

    <div class="col-12 text-center">
        <x-adminlte-button label="Submit" theme="primary" class="Bin.update" icon="fas fa-save" type="submit" />
    </div>
    </form>
</div>
<div class="col"></div>
</div>

@stop