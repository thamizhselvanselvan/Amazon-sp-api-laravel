@extends('adminlte::page')

@section('title', 'Promo Section')

@section('css')
<link rel="stylesheet" href="/css/styles.css">
@stop

@section('content_header')
<div class="row">
    <div class="col=1"></div>
</div>
<div class="row justify-content-center">
    <h3>Cliqnshop Promo Banner Section </h3>
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
<form method="post" action="{{route('cliqnshop.promo.banner.store')}}" enctype="multipart/form-data">
    @csrf
    <div class="row justify-content-center">
        <div class="col-3">
            <div class="form-group">
                <x-adminlte-select name="country" label="Select Country" name="country">
                    <option value=''>Select Country</option>
                    @foreach ($countrys as $country)
                    <option value="{{ $country->siteid }}">{{$country->code }}</option>
                    @endforeach
                </x-adminlte-select>
            </div>
        </div>
        <div class="col-3">
            <div class="form-group">
                <label for="text">Offer Text</label>
                <x-adminlte-input class="form-control" type="text" placeholder="Enter offer Text" name="offer_text" id="" />
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-3">
            <div class="form-group">
                <label for="text">Primary Text</label>
                <x-adminlte-input class="form-control" type="text" placeholder="Enter primary Text" name="primary_text" id="" />
            </div>
        </div>
        <div class="col-3">
            <div class="form-group">
                <label for="text">secondary Text</label>
                <x-adminlte-input class="form-control" type="text" placeholder="Enter secondary Text" name="secondary_text" id="" />
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-3">
            <div class="form-group">
                <label for="text">Redirection URL</label>
                <x-adminlte-input class="form-control" type="text" placeholder="Enter Redirect URL For Image" name="url" id="url" />
            </div>
        </div>
        <div class="col-3">
            <div style="margin-top: 2.0rem;">
                <x-adminlte-button label="Submit" theme="primary" id="img_submit" icon="fas fa-upload" type="submit" />
            </div>
        </div>
    </div>
</form>
@stop


@section('js')
<script type="text/javascript">


</script>
@stop