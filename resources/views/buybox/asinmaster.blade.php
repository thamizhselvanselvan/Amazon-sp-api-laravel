@extends('adminlte::page')
@section('title', 'Import')

@section('content_header')
<h1 class="m-0 text-dark">Buy Box Asin Master</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-2">
            <x-adminlte-select name="source" id="source" label="Select Source">
                <option value="0">Select Source</option>
                @foreach ($country as $country_list)
                <option value="{{$country_list->id}}">{{$country_list->region_code}}</option>
                @endforeach
            </x-adminlte-select>
        </div>
        <div class="col-2">
            <x-adminlte-select name="destination" id="destination" label="Select Destination">
                <option value="0">Select Destination</option>
                @foreach ($country as $country_list)
                <option value="{{$country_list->id}}">{{$country_list->region_code}}</option>
                @endforeach
            </x-adminlte-select>
        </div>
    </div>
@stop

@section('js')

@stop