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

        <form action="{{ route('warehouses.update', $name->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row justify-content-center">

                <div class=" col-6">
                    <x-adminlte-input label="name" name="name" value="{{$name->name }}" type="text" />
                </div>
                <div class="col-6">
                    <x-adminlte-input label="Address 1" name="address_1" value="{{$name->address_1 }}" type="text" />
                </div>
                <div class="col-6">
                    <x-adminlte-input label="Address 2" name="address_2" value="{{$name->address_2 }}" type="text" />
                </div>
                <div class="col-6">
                    <x-adminlte-select label="Country" id="country" name="country" type="text">
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
                    <x-adminlte-select label="State" id="state" name="state" type="text">
                        <option value=" ">Select State</option>

                    </x-adminlte-select>
                </div>
                <div class="col-6">
                    <x-adminlte-select label="city" name="city" type="text">
                        <option value=" ">Select City</option>

                    </x-adminlte-select>
                </div>
            </div>
            <div class="row justify-content-left">
                <div class="col-6">
                    <x-adminlte-input label="Pin Code" name="pin_code" type="text" value="{{ $name->pin_code}}" />
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-6">
                    <x-adminlte-input label="Contact person name" name="contact_person_name" type="text" value="{{ $name->contact_person_name }}" />
                </div>
                <div class="col-6">
                    <x-adminlte-input label="Phone No" name="phone_number" type="text" value="{{ $name->phone_number }}" />
                </div>
            </div>
            <div class="row justify-content-left">
                <div class="col-6">
                    <x-adminlte-input label="Email" name="email" type="text" value="{{ $name->email }}" />
                </div>
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
<script>
    $(document).ready(function(e) {
        var country_id = $('#country').val();
        statechange(country_id);
    });

    setTimeout(function() {
        var state_id = $('#state').val();
        if (state_id != null) {
            citychange(state_id);
        }
    }, 5000);

    $('#country').on('change', function(e) {
        e.preventDefault();
        var id = $(this).val();
        statechange(id);

    });
    $('#state').on('change', function(e) {
        e.preventDefault();
        var id = $(this).val();
        citychange(id);

    });

    function statechange(id) {

        $.ajax({
            method: 'POST',
            url: '/json/' + id,
            data: {
                'id': id,
                "_token": "{{ csrf_token() }}",
            },

            response: 'json',
            success: function(response) {
                $('#state').empty();
                let state_data = '<option >Select State</option>';
                $.each(response, function(i, response) {
                    let selected_state = "{{ isset($selected_state) ? $selected_state : null }}";
                    let selected = (selected_state == response.id) ? 'selected' : '';
                    state_data += "<option value='" + response.id + "'  " + selected + ">" + response.name + "</option>";
                });
                $('#state').append(state_data);

            },

            error: function(response) {
                alert('something went wrongg..');
            }

        });



    }

    function citychange(id) {
        $.ajax({
            method: 'POST',
            url: '/stateId/' + id,
            data: {
                'id': id,
                "_token": "{{ csrf_token() }}",
            },
            success: function(result) {
                $('#city').empty();
                let city_data = '<option >Select City</option>';
                $.each(result, function(i, result) {
                    let selected_city = "{{ isset($selected_city) ? $selected_city : null}}";
                    let selected = (selected_city == result.id) ? 'selected' : '';
                    city_data += "<option value='" + result.id + "' " + selected + ">" + result.name + "</option>";
                });
                $('#city').append(city_data);

            },
            error: function(response) {
                alert('something went wrongg..');
            }
        });
    }
</script>

@stop