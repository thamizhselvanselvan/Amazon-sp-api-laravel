@extends('adminlte::page')

@section('title', 'Add Currency')

@section('css')
<link rel="stylesheet" href="/css/styles.css">
@stop

@section('content_header')

<div class="row">
    <div class="col">
        <a href="{{ Route('currency.home') }}" class="btn btn-primary">
            <i class="fas fa-long-arrow-alt-left"></i> Back
        </a>
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

        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Add Currency</h3>
            </div>


            <form action="{{route('currency.home')}}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <x-adminlte-input label="Name" name="currency" id="name" type="text" placeholder="Currency Name" value="" />
                    </div>
                    <div class="form-group">
                        <x-adminlte-input label="Code" name="code" id="code" type="text" placeholder="Currency Code" value="" />
                    </div>
                    <div class="form-group">
                    <x-adminlte-select name="status" label="Status">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </x-adminlte-select>
                    </div>
                </div>
                <div class="card-footer">
                    <x-adminlte-button label="Add Currency" theme="primary" icon="fas fa-plus" type="submit" />
                </div>
            </form>
        </div>
    </div>
    <div class="col"></div>
</div>

@stop