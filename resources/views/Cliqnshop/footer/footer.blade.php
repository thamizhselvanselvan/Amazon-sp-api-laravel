@extends('adminlte::page')

@section('title', 'Footer')

@section('css')
    <link rel="stylesheet" href="/css/styles.css">
@stop

@section('content_header')
    <div class="row">
        <div class="col=1"></div>
    </div>
    <div class="row ">
        <h3>Footer Section</h3>
    </div>

@stop

@section('content')
    @if (session()->has('success'))
        <div class="alert alert-success" role="alert">
            <button type="button" class="close" data-dismiss="alert">×</button>
            {{ session()->get('success') }}
        </div>
    @endif
    <form action="/cliqnshop/footercontent" method="POST">
        @csrf
        <center>
            <div class="form-group w-25">
                <x-adminlte-select name="site" id="" label="">
                    <option value="" selected>Select Site</option>
                    <option value="in">India</option>
                    <option value="uae">UAE</option>
                </x-adminlte-select>
                <x-adminlte-select name="section" id="" label="">
                    <option value="" selected>Select Section</option>
                    <option value="Call Us">Call Us</option>
                    <option value="Email for Us">Email for Us</option>
                    <option value="Facebook">Facebook link</option>
                    <option value="Twitter">Twitter link</option>
                    <option value="Instagram">Instagram link</option>
                    <option value="Youtube">Youtube link</option>
                </x-adminlte-select>
                <div>
                    <x-adminlte-input label="" name="content" id="" type="text"
                        placeholder="Enter Content" />
                </div>
                <x-adminlte-button class="btn-flat" type="submit" label="Submit" theme="success"
                    icon="fas fa-lg fa-save" />
        </center>
    </form>
@stop

@section('js')

    $(document).on('click', '.delete', function() {
    let bool = confirm('Are you sure you want to delete this ?');
    if (!bool) {
    return false;
    }
});
@stop
