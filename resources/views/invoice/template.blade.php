@extends('adminlte::page')
@section('title', 'template')

@section('content_header')

@stop

@section('content')

    <div>
        @foreach ($data as $key => $value )
        {
            {{$key}}
            <hr>
        }
            
        @endforeach
    </div>

@stop