@extends('adminlte::page')

@section('title', 'Add Vendor')

@section('css')
<link rel="stylesheet" href="/css/styles.css">
@stop

@section('content_header')

<div class="row">
    <div class="col">
        <a href="{{ route('vendors.index') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-long-arrow-alt-left"></i> Back
        </a>
    </div>
</div>

<div class="row mt-3">
    <div class="col">
        <h3 class="m-0 text-dark text-center">Add Source/Destination</h3>
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

        <form action="{{ route('vendors.store') }}" method="POST" id="admin_user">

            @csrf

            <div class="row justify-content-center">
                <div class="col-6">
                    <x-adminlte-input label="Name" name="name" id="" type="text" placeholder="Name " />
                </div>
                <div class="col-6">
                    <x-adminlte-select name="type" label="Select type">
                            <option>Select Type</option>
                            <option>Source</option>
                            <option>Destination</option>
                        </x-adminlte-select>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-6">
                    <x-adminlte-select label="Country" name="country" id="country" type="text" placeholder="" >
                    <option value="">Select Country</option>
                        @foreach ($country as $countries)
                            <option value="{{$countries->id}}">{{$countries->name}}</option>
                        @endforeach
                        
                    </x-adminlte-select>
                </div>
                <div class="col-6">
                    <x-adminlte-select label="State" name="state" id="state" type="text" placeholder="" >
                        <option value="">Select State</option>
                    </x-adminlte-select>
                </div>
                 <div class="col-6">
                    <x-adminlte-select label="City" name="city" id="city" type="text" placeholder="" >
                        <option value="">Select City</option>
                    </x-adminlte-select>
                </div>
                
               <div class="col-6">
                    <x-adminlte-select label="Currency" name="currency" id="currency" type="text" placeholder="" >
                        <option value="">Select Currency</option>

                        @foreach ($currency_lists as $currency_list)
                            <option value="{{ $currency_list->id }}">{{$currency_list->code }}</option>
                        @endforeach
                        
                    </x-adminlte-select>
                </div>
            </div>


            <div class="text-center">
                <x-adminlte-button label="Submit" theme="primary" icon="fas fa-plus" type="submit" />
            </div>
        </form>
    </div>
    <div class="col"></div>
</div>

@stop

@section('js')

<script>
    
    $(document).ready(function(){

        $('#country').change(function(e){
            e.preventDefault();
            var id=$(this).val();
            
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
                }
            });
        });
    });

</script>
@stop
