@extends('adminlte::page')

@section('title', 'Edit Warehouse')

@section('css')

    <link rel="stylesheet" href="/css/styles.css">

@stop

@section('content_header')

    <div class="row">
        <div class="col">
            <a href="{{ Route('warehouses.index') }}" class="btn btn-primary">

                <i class="fas fa-long-arrow-alt-left"></i> Back
            </a>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col">
            <h1 class="m-0 text-dark text-center ">Edit Warehouse</h1>
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

            <form  action="{{ route('warehouses.update', $name->id) }}" method="POST">
                @csrf
                @method('PUT')
    
                <div class="row justify-content-center">
            
                    <div class=" col-6">
                        <x-adminlte-input label="name" name="name" value="{{$name->name }}" type="text" />
                    </div>
                    <div class="col-6">
                        <x-adminlte-input label="Address 1" name="address_1"  value="{{$name->address_1 }}" type="text" />
                    </div>
                    <div class="col-6">
                        <x-adminlte-input label="Address 2" name="address_2"   value="{{$name->address_2 }}" type="text" />
                    </div>
                    <div class="col-6">
                        <x-adminlte-select label="Country" id="country" name="country" type="text">
                            <option >Select Country</option>
                            @foreach ($country as $countries)
                            <option value="{{($countries->country_name) }}">{{$countries->country_name }}</option>
                            @endforeach
                        </x-adminlte-select>
                    </div>
                    <div class="col-6">
                        <x-adminlte-select label="State" id="state" name="state" type="text">
                            <option >Select State</option>
                        </x-adminlte-select>
                    </div>
                    <div class="col-6">
                        <x-adminlte-select label="city" name="city" type="text"> 
                            <option >Select City</option>
                        </x-adminlte-select>
                    </div>
                </div>
                    <div class="col-6">
                        <x-adminlte-input label="Pin Code" name="pin_code" type="text" 
                            value="{{ $name->pin_code}}" />
                    
                </div>
    
                <div class="row justify-content-center">
                    <div class="col-6">
                        <x-adminlte-input label="Contact person name" name="contact_person_name" type="text"
                        value="{{ $name->contact_person_name }}"/>
                    </div>
                    <div class="col-6">
                        <x-adminlte-input label="Phone No" name="phone_number" type="text" 
                            value="{{ $name->phone_number }}" />
                    </div>
                </div>
                    <div class="col-6">
                        <x-adminlte-input label="Email" name="email" type="text" 
                        value="{{ $name->email }}" />
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
@section('js')
    <script >

$(document).ready(function(){

$('#country').change(function(e){
    e.preventDefault();
   var cname=$(this).val();
    // alert(cname);
        // $('#state').val(id);

    $.ajax({
        method:'POST',
        url:'/json/'+cname,
        data:{ 
            'cname':cname,
            "_token": "{{ csrf_token() }}",
        },
       
        response:'json',
        success:function(response){
           
        $('#state').empty();
            let state_data ='<option >Select State</option>';
        $.each(response ,function(i,response){
            state_data+= "<option value='"+response.state_name+"'>"+response.state_name+"</option>";
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
    var sname =$(this).val();
    // var countryid=$('#country').val();
    // alert(countryid);
    // alert(sname);

    $.ajax({
        method:'POST',
        url:'/stateId/'+sname,
        data:{
            'sname':sname,
            "_token": "{{ csrf_token() }}",
        },
        success:function(result){
               
        $('#city').empty();
            let city_data ='<option >Select City</option>';
        $.each(result ,function(i,result){
            city_data+= "<option value='"+result.city_name+"'>"+result.city_name+"</option>";
        });
        $('#city').append(city_data);
        
        },
    });
});

});



</script> 

@stop  
