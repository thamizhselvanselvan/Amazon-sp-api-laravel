@extends('adminlte::page')

@section('title', 'Add State')

@section('css')
    <link rel="stylesheet" href="/css/styles.css">
@stop

@section('content_header')

    <div class="row">
        <div class="col">
            <a href="{{ route('geo.state') }}" class="btn btn-primary">
                <i class="fas fa-long-arrow-alt-left"></i> Back
            </a>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col">
            <h1 class="m-0 text-dark text-center">Add State</h1>
        </div>
    </div>

@stop

@section('content')

    <div class="ml-5 mr-5 p-2 bg-gradient-light text-white rounded">
        <!-- <form class="text-center" method="POST" action="{{ route('geo.store.state') }}"> -->
        <form class="text-center" method="POST" action="{{ route('geo.state') }}">
            @csrf
            <div class="m-2">
                <select class="form-control w-25 m-auto" name="country_id" aria-label="Default select example" required>
                    <option value="" selected>Select Country</option>
                    @foreach ($countries as $country1)
                        <option id="country_id" value="{{ $country1->id }}">{{ $country1->name }}</option>
                    @endforeach
                </select>
                <span class="text-danger">
                    @error('country_id')
                        {{ $message = 'Please Select Country' }}
                    @enderror
                    <br>
                    <input type="text" class="form-control w-25 m-auto" id="name" name="name" placeholder="State"
                        autofocus required autocomplete="off">
                    <span class="text-danger">
                        @error('name')
                            {{ $message = 'This State already exist' }}
                        @enderror
            </div>
            <br>
            <x-adminlte-button label=" Submit" theme="primary" icon="fas fa-plus" type="submit" />
        </form>
    </div>

@stop
