@extends('adminlte::page')
@section('title', 'Import')

@section('content_header')
<h1 class="m-0 text-dark"> Download Files</h1>
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
                <form class="container">
                    <li>
                        <ul>    
                            <a href="{{route('download.universalTextiles')}}">
                                Download Universal Textils
                            </a>
                        </ul>
                    </li>
                </form>
        </div>
    </div>

@stop

@section('js')
   
@stop