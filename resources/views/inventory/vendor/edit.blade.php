@extends('adminlte::page')

@section('title', 'Edit Vendor')

@section('css')

<link rel="stylesheet" href="/css/styles.css">

@stop

@section('content_header')

<div class="row">
    <div class="col">
        <a href="{{ route('vendors.index') }}" class="btn btn-primary">

            <i class="fas fa-long-arrow-alt-left"></i> Back
        </a>
    </div>
</div>

<div class="row mt-3">
    <div class="col">
        <h1 class="m-0 text-dark text-center ">Edit vendor</h1>
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
    <div class="col-8">

        @if (session()->has('success'))
        <div class="alert alert-success">
            {{ session()->get('success') }}
        </div>
        @endif

        @if(session()->has('error'))
        <div class="alert alert-danger">
            {{ session()->get('error') }}
        </div>
        @endif

        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('vendors.update', $name->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row justify-content-center">
                <div class="col-6">
                    <x-adminlte-select name="type" label="Select type" value="{{ $name->type  }}">
                        <option>Select Type</option>
                        @if ($name->type == "Source")
                        <option value="{{$name->type}}" selected>{{$name->type}}</option>
                        <option value="Destination">Destination</option>
                        @else
                        <option value="{{$name->type}}" selected>{{$name->type}}</option>
                        <option value="Source">Source</option>
                        @endif
                    </x-adminlte-select>
                </div>
                <div class="col-6">
                    <x-adminlte-input label="Name" name="name" value="{{ $name->name }}" type="text" />
                </div>

            </div>
            <div class="row justify-content-center">
                <div class="col-6">
                    <x-adminlte-select label="Country" name="country" id="country" type="text" placeholder="" value="{{ $name->country  }}">
                        <option value="">Select Country</option>
                        @foreach ($country as $countries)
                        @if ($countries->id == $selected_country)
                        <option value="{{$countries->id}}" selected>{{$countries->name}}</option>
                        @else
                        <option value="{{$countries->id}}">{{$countries->name}}</option>
                        @endif

                        @endforeach

                    </x-adminlte-select>
                </div>
                <div class="col-6">
                    <x-adminlte-select label="State" name="state" id="state" type="text" placeholder="" value="{{ old('ID') }}">
                        <option value="">Select Country</option>
                        @foreach ($state as $states)

                        @if ($states->country_id == $selected_country)

                        @if (($states->id == $selected_state))
                        <option value="{{$states->id}}" selected>{{$states->name}}</option>
                        @else
                        <option value="{{$states->id}}">{{$states->name}}</option>
                        @endif

                        @endif

                        @endforeach
                    </x-adminlte-select>
                </div>
                <div class="col-6">
                    <x-adminlte-select label="City" name="city" id="city" type="text" placeholder="" value="{{ old('ID') }}">
                        <option value="">Select Country</option>
                        @foreach ($city as $cities)

                        @if ($cities->state_id == $selected_state)

                        @if (($cities->id == $selected_city))
                        <option value="{{$cities->id}}" selected>{{$cities->name}}</option>
                        @else
                        <option value="{{$cities->id}}">{{$cities->name}}</option>
                        @endif

                        @endif

                        @endforeach
                    </x-adminlte-select>
                </div>
                <div class="col-6">
                    <x-adminlte-select label="Currency" name="currency_id" type="text" value="{{  $name->currency }}">
                        <option value="">Select Currency</option>
                        @foreach ($currencies as $currency)

                        @if ($currency->id == $selected_currency)
                        <option value="{{$currency->id}}" selected> {{$currency->code}} </option>
                        @else
                        <option value="{{$currency->id}}"> {{$currency->code}} </option>
                        @endif

                        @endforeach
                    </x-adminlte-select>
                </div>
            </div>
            <div class="col-3"></div>
            <div class="col-12 text-center">
                <x-adminlte-button label="Submit" theme="primary" class="rack.update" icon="fas fa-save" type="submit" />
            </div>
        </form>
    </div>
    <div class="col"></div>
</div>

@stop

<!-- @section('js')
    <script>
        
        $(document).ready(function(){

            $('#country').on('change',function(e){
                e.preventDefault();
                var id=$(this).val();
                // alert(id);
                    // $('#state').val(id);

                $.ajax({
                    method:'POST',
                    url:'/vendor/'+id,
                    data:{ 
                        'id':id,
                        "_token": "{{ csrf_token() }}",
                    },
                
                    response:'json',
                    success:function(response){
                    
                    $('#state').empty();
                        let state_data ='<option >Select State</option>';
                    $.each(response ,function(i,response){
                        
                        state_data+= "<option value='"+response.id+"'selected>"+response.name+"</option>";
                        
                    });
                    $('#state').append(state_data);
                    
                    },
                    failure:function(response){
                        console.log(response);
                    },
                    error:function(response){
                        console.log(response);
                    }
                    
                });
            });

            $('#state').change(function(e){
                e.preventDefault();
                var id =$(this).val();
                // var countryid=$('#country').val();
                // alert(countryid);
                // alert(sname);

                $.ajax({
                    method:'POST',
                    url:'/vendorstate/'+id,
                    data:{
                        'id':id,
                        "_token": "{{ csrf_token() }}",
                    },
                    success:function(result){
                        
                    $('#city').empty();
                        let city_data ='<option >Select City</option>';
                    $.each(result ,function(i,result){
                        city_data+= "<option value='"+result.id+"'>"+result.name+"</option>";
                    });
                    $('#city').append(city_data);
                    
                    },
                });
            });

        });

    </script>
@stop -->