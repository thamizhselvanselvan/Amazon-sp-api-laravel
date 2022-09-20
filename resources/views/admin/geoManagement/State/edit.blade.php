@extends('adminlte::page')

@section('title', 'Update State')

@section('css')
<link rel="stylesheet" href="/css/styles.css">
@stop

@section('content_header')

<div class="row">
    <div class="col">
        <a href="/admin/geo/state" class="btn btn-primary">
            <i class="fas fa-long-arrow-alt-left"></i> Back
        </a>
    </div>
</div>

<div class="row mt-3">
    <div class="col">
        <h1 class="m-0 text-dark text-center">Update State</h1>
    </div>
</div>

@stop

@section('content')

<div class = "ml-5 mr-5 p-2 bg-gradient-light text-white rounded">
  <form class="text-center" method = "POST" action="/update_state/{{$states->id}}">
    @csrf
  <div class="m-2">
  <select class="form-control w-25 m-auto" name ="country" aria-label="Default select example">
  <option selected>Select Country</option>
  @foreach($countries as $country1)
  <option value="{{$country1->id}}" {{$country1->id == $states->country_id ? 'selected' : '' }}>{{$country1->name}}</option>
  @endforeach
</select>
<br>
    <input type="text" class="form-control w-25 m-auto" id="state" name ="state_name" value= {{$states->name}} placeholder = "State" autofocus required autocomplete="off">
    <span class="text-danger">
      @error('state_name')
      {{$message="This State already exist"}}
      @enderror
  </div>
  <br>
  <x-adminlte-button label=" Submit" theme="primary" icon="fas fa-plus" type="submit" />
</form>
</div>

@stop