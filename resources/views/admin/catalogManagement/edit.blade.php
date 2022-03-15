@extends('adminlte::page')

@section('title', 'Edit Sellers')

@section('css')

<link rel="stylesheet" href="/css/styles.css">

@stop

@section('content_header')

    <div class="row">
        <div class="col">
            <a href="{{ Route ('admin.catalog_user') }}" class="btn btn-primary">
                <i class="fas fa-long-arrow-alt-left"></i> Back
            </a>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col">
            <h1 class="m-0 text-dark text-center">Edit Catalog User</h1>
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

        @if(session()->has('success'))
            <div class="alert alert-success">
                {{ session()->get('success') }}
            </div>
        @endif
     
        <form action="{{Route('catalog_user.update',$user->id)}}" method="POST" id="update_catalog_user">
            @csrf
            @method('POST')

            <div class="row">
                <div class="col-6">
                    <x-adminlte-input label="Name" name="name" id="name" value="{{ $user->name }}" type="text" placeholder="Name"/>
                </div>
                <div class="col-6">
                    <x-adminlte-input label="Email" name="email" id="email" value="{{ $user->email }}" type="text" placeholder="Email"/>
                </div>
            </div>
         
            <div class="text-center">
                <x-adminlte-button label="Edit Seller" theme="primary" class="edit_seller" icon="fas fa-save" type="submit" />
            </div>
        </form>
    </div>
    <div class="col"></div>
</div>

@stop




