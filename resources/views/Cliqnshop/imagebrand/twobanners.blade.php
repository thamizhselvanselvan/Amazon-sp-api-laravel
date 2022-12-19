@extends('adminlte::page')

@section('title', 'Banner Section')

@section('css')
<link rel="stylesheet" href="/css/styles.css">
@stop

@section('content_header')
<div class="row">
    <div class="col=1"></div>
</div>
<div class="row justify-content-center">
    <h3>Cliqnshop 2 Banner Section </h3>
</div>

@stop

@section('content')
<div class="row">
    <div class="col">
        <div class="alert_display">
            @if ($message = Session::get('success'))
            <div class="alert alert-success alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $message }}</strong>
            </div>
            @endif
        </div>
        <div class="alert_display">
            @if ($message = Session::get('error'))
            <div class="alert alert-danger alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $message }}</strong>
            </div>
            @endif
        </div>
    </div>
</div>
<form method="post" action="{{route('cliqnshop.two.banner.store')}}" enctype="multipart/form-data">
    @csrf
    <div class="row justify-content-center">
        <div class="col-3">
            <div class="form-group">
                <x-adminlte-select name="image_number" label="Select Image">
                    <option>Select Image No.</option>
                    <option>Image-1</option>
                    <option>Image-2</option>

                </x-adminlte-select>
            </div>
        </div>

        <div class="col-2">
            <div class="form-group">
                <x-adminlte-select name="country" label="Select Country" name="country">
                     <option>Select Country</option>
                      @foreach ($countrys as $country)
                     <option value="{{ $country->siteid }}">{{$country->code }}</option>
                      @endforeach
                </x-adminlte-select>
            </div>

        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-5">
            <div class="form-group">
                <label for="text">Redirection URL</label>
                <x-adminlte-input class="form-control " type="text" placeholder="Enter Redirect URL For Image" name="url" id="url" />
            </div>
        </div>
    </div>
    <div class="row justify-content-center">

        <div class="col-5">
            <div class="form-group">
                <x-adminlte-input type="file" name="selected_image" label="Select Image"></x-adminlte-input>
            </div>

        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-2">
            <x-adminlte-button label="Submit" theme="primary" id="img_submit" icon="fas fa-upload" type="submit" />
        </div>
    </div>
</form>
@stop


@section('js')
<script type="text/javascript">


</script>
@stop