@extends('adminlte::page')

@section('title', 'Update Company')

@section('css')
    <link rel="stylesheet" href="/css/styles.css">
@stop

@section('content_header')

    <div class="row">
        <div class="col">
            <a href="{{ Route('company') }}" class="btn btn-primary">
                <i class="fas fa-long-arrow-alt-left"></i> Back
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <h1 class="text-dark">Update Company</h1>
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

            <form action="{{route('company.update',$company->id)}}" method="POST" id="admin_user">


                @csrf

                <div class="row">
                    <div class="col-6">
                        <x-adminlte-input label="Company Name" name="company_name" id="name" type="text" placeholder="Name"
                            value="{{$company->company_name}}" />
                    </div>
                
                </div>

                <div class="text-center row">
                    <div class="col-6">
                        <x-adminlte-button label="Edit Company" theme="primary" icon="fas fa-plus" type="submit" />

                    </div>
                </div>
            </form>
        </div>
        <div class="col"></div>
    </div>

@stop
