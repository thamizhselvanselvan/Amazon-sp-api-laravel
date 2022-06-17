@extends('adminlte::page')

@section('title', 'Add Warehouse')

@section('css')
    <link rel="stylesheet" href="/css/styles.css">
    @stop

@section('content_header')

    <div class="row">
        <div class="col">
             <a href="{{ route('warehouses.index') }}" class="btn btn-primary">  
                <i class="fas fa-long-arrow-alt-left"></i> Back
            </a>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col">
            <h1 class="m-0 text-dark text-center">Add Warehouse</h1>
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
        <div class="col-6">

            @if (session()->has('success'))
                <x-adminlte-alert theme="success" title="Success" dismissable>
                    {{ session()->get('success') }}
                </x-adminlte-alert>
            @endif

            @if (session()->has('error'))
                <x-adminlte-alert theme="danger" title="Error" dismissable>
                    {{ session()->get('error') }}
                </x-adminlte-alert>
            @endif

             <form action="{{ route('warehouses.store') }}" method="POST" id="admin_user">
               @csrf

                <div class="row justify-content-center">
                    <div class="col-6">
                        <x-adminlte-input label="Name" name="name" type="text" placeholder="Name"
                            value="{{ old('name') }}" />
                    </div>

                    <div class="col-6">
                        <x-adminlte-input label="Address 1" name="address_1" type="text" placeholder="Address 1"
                            value="{{ old('name') }}" />
                    </div>
                    <div class="col-6">
                        <x-adminlte-input label="Address 2" name="address_2" type="text" placeholder="Address 2"
                            value="{{ old('name') }}" />
                    </div>
                    <div class="col-6">
                        
                        <x-adminlte-select name="country" label="Select Country:" id="country">
                            <option value="">Select Country</option>
                            @foreach ($country as $countries)
                            <option value="{{ ($countries->id) }}">{{$countries->name}}</option>
                            @endforeach
                        </x-adminlte-select>
                        
                        
                    </div>
                    <div class="col-6">
                        <x-adminlte-select label=" Select State" id="state" name="state" type="text" placeholder="State">
                            <option value="" > Select State</option>
                        </x-adminlte-select>
                            
                    </div>
                    <div class="col-6">
                        <x-adminlte-select label=" Select City" id="city" name="city" type="text" placeholder="City">
                            <option value=""> Select City</option>
                        </x-adminlte-select>
                    </div>
                </div>
                <div class="row justify-content-left">
                    <div class="col-6">
                        <x-adminlte-input label="Pin Code" name="pin_code" type="text" placeholder="Pin Code"
                            value="{{ old('name') }}" />
                    </div>
                </div>
    
                <div class="row justify-content-center">
                    <div class="col-6">
                        <x-adminlte-input label="Contact person name" name="contact_person_name" type="text" placeholder="Person"
                            value="{{ old('name') }}" />
                    </div>
                    <div class="col-6">
                        <x-adminlte-input label="Phone No" name="phone_number" type="text" placeholder="Phone No"
                            value="{{ old('name') }}" />
                    </div>
                </div>
                <div class="row justify-content-left">
                    <div class="col-6">
                        <x-adminlte-input label="Email" name="email" type="text" placeholder="Email"
                            value="{{ old('name') }}" />
                    </div>
                </div>

                <div class="text-center">
                    <x-adminlte-button label=" Submit" theme="primary" icon="fas fa-plus" type="submit" />
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
           var id=$(this).val();
            // alert(id);
                // $('#state').val(id);

            $.ajax({
                method:'POST',
                url:'/json/'+id,
                data:{ 
                    'id':id,
                    "_token": "{{ csrf_token() }}",
                },
               
                response:'json',
                success:function(response){
                   
                $('#state').empty();
                    let state_data ='<option >Select State</option>';
                $.each(response ,function(i,response){
                    state_data+= "<option value='"+response.id+"'>"+response.name+"</option>";
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
                url:'/stateId/'+id,
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

@stop  


