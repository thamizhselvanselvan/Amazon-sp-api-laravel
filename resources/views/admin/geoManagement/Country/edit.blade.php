@extends('adminlte::page')

@section('title', 'Update Country')

@section('css')
<link rel="stylesheet" href="/css/styles.css">
@stop

@section('content_header')

<div class="row">
    <div class="col">
        <a href="/admin/geo/country" class="btn btn-primary">
            <i class="fas fa-long-arrow-alt-left"></i> Back
        </a>
    </div>
</div>

<div class="row mt-3">
    <div class="col">
        <h1 class="m-0 text-dark text-center">Update Country</h1>
    </div>
</div>

@stop

@section('content')
  <form class="form-group" method = "POST" action="/update_country/{{$countries->id}}">
    <div class="container">
      @csrf
      <div class="row">
        <div class="col-sm">
          <label class="ml-2 m-0" for="country">Country</label>
        <input type="text" class="form-control m-2" id="country" name ="name" placeholder = "Country" required autofocus autocomplete="off" value= {{$countries->name}}>
        </div>
        <div class="col-sm">
          <label class="ml-2 m-0" for="country_code">Country Code</label>
          <input type="text" class="form-control m-2" id="country_code" name ="country_code" placeholder = "Country Code" required autocomplete="off" value={{$countries->country_code}}>
        </div>
        <div class="col-sm">
           <label class="ml-2 m-0" for="code">Code</label>
          <input type="text" class="form-control m-2" id="code" name ="code" placeholder = "Code" required autocomplete="off" value={{$countries->code}}>
        </div>
      </div>
    </div>
    <div class="container">
      <div class="row">
        <div class="col-sm">
         <p class="text-danger ml-4 mt-0 mb-0 mr-0"> @error('name')
          {{$message}}
          @enderror</p>
        </div>
        <div class="col-sm">
          <p class="text-danger ml-4 mt-0 mb-0 mr-0"> @error('country_code')
          {{$message}}
          @enderror
          </p>
        </div>
        <div class="col-sm">
          <p class="text-danger ml-4 mt-0 mb-0 mr-0"> @error('code')
          {{$message}}
          @enderror
          </p>
        </div>
      </div>
    </div>
    <div class="container">
      <div class="row">
        <div class="col-sm">
           <label class="ml-2 m-0" for="numeric_code">Numeric Code</label>
          <input type="text" class="form-control m-2" id="numeric_code" name ="numeric_code" placeholder = "Numeric Code" required autocomplete="off" value={{$countries->numeric_code}}>
        </div>
        <div class="col-sm">
           <label class="ml-2 m-0" for="phone_code">Phone Code</label>
          <input type="text" class="form-control m-2" id="phone_code" name ="phone_code" placeholder = "Phone Code" required autocomplete="off" value={{$countries->phone_code}}>
        </div>
        <div class="col-sm">
           <label class="ml-2 m-0" for="capital">Capital</label>
          <input type="text" class="form-control m-2" id="capital" name ="capital" placeholder = "Capital" required autocomplete="off" value={{$countries->capital}}>
        </div>
    </div>
  </div>
  <div class="container">
    <div class="row">
      <div class="col-sm">
        <p class="text-danger ml-4 mt-0 mb-0 mr-0"> @error('numeric_code')
          {{$message}}
          @enderror
        </p>
      </div>
      <div class="col-sm">
        <p class="text-danger ml-4 mt-0 mb-0 mr-0"> @error('phone_code')
          {{$message}}
          @enderror
        </p>
      </div>
      <div class="col-sm">
        <p class="text-danger ml-4 mt-0 mb-0 mr-0"> @error('capital')
          {{$message}}
          @enderror
        </p>
      </div>
    </div>
  </div>
    <div class="container">
      <div class="row">
        <div class="col-sm">
           <label class="ml-2 m-0" for="currency">Currency</label>
          <input type="text" class="form-control m-2" id="currency" name ="currency" placeholder = "Currency" required autocomplete="off" value={{$countries->currency}}>
        </div>
        <div class="col-sm">
           <label class="ml-2 m-0" for="currency_name">Currency Name</label>
          <input type="text" class="form-control m-2" id="currency_name" name ="currency_name" placeholder = "Currency Name" required autocomplete="off" value={{$countries->currency_name}}>
        </div>
        <div class="col-sm">
           <label class="ml-2 m-0" for="currency_symbol">Currency Symbol</label>  
      <input type="text" class="form-control m-2" id="currency_symbol" name ="currency_symbol" placeholder = "Currency Symbol" required autocomplete="off" value="{{$countries->currency_symbol}}">
        </div>
      </div>
    </div>
    <div class="container">
      <div class="row">
        <div class="col-sm">
          <p class="text-danger ml-4 mt-0 mb-0 mr-0">  @error('currency')
          {{$message}}
          @enderror
          </p>
        </div>
        <div class="col-sm">
          <p class="text-danger ml-4 mt-0 mb-0 mr-0"> @error('currency_name')
          {{$message}}
          @enderror
          </p>
        </div>
        <div class="col-sm">
          <p class="text-danger ml-4 mt-0 mb-0 mr-0">  @error('currency_symbol')
      {{$message}}
      @enderror
          </p>
        </div>
      </div>
    </div>
    <div class="container">
      <div class="row">
        <div class="m-auto">
      <x-adminlte-button label=" Submit" theme="primary" icon="fas fa-plus" type="submit" />
      </div>
      </div>
      </div>
  </form>
  </div>
  </div>
@stop