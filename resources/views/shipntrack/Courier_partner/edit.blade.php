@extends('adminlte::page')

@section('title', 'Edit Courier Partner')

@section('css')

<link rel="stylesheet" href="/css/styles.css">

@stop

@section('content_header')

<div class="row">
    <div class="col-3"></div>
    <div class="col-0.5">
        <a href="{{ route('snt.courier.index') }}" class="btn btn-primary">
            <i class="fas fa-long-arrow-alt-left"></i> Back
        </a>
    </div>


    <div class="col-5">
        <h1 class="m-0 text-dark text-center">Edit Courier Partner</h1>
    </div>
</div>

@stop

@section('content')
<div class="row">
    <div class="col-3"></div>
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

        <form action="{{ route('courier.partners.update',$data->id) }}" method="POST" id="admin_user">
            @csrf

            <div class="row justify-content-center">
                <div class="col-6">
                    <x-adminlte-input label="User Name" name="user_name" type="text" placeholder="user_name" value="{{ $data->user_name }}" />
                </div>
                <div class="col-6">
                    <!-- <x-adminlte-input label="Courier Name" name="name" type="text" placeholder="Name" value="{{ old('name') }}" /> -->


                    <x-adminlte-select label="Courier" name="courier_name" id="courier_name" type="text" value="{{ old('courier_name') }}">
                        <option value="">Select Courier</option>
                        @foreach ($couriers as $Courier)
                        @if ($Courier->id == $selected_courier)
                        <option value="{{ $Courier->id }}" selected> {{ $Courier->courier_name }}</option>
                        @else
                        <option value="{{ $Courier->id  }}">{{$Courier->courier_name }}</option>
                        @endif
                        <!-- <option value="{{$Courier->id}}">{{$Courier->courier_name}}</option> -->
                        @endforeach

                    </x-adminlte-select>

                </div>
                <div class="col-6">
                    <x-adminlte-select name="status" label="Select status:" id="status">
                        <!-- <option value="{{$data->status ?? ''? $data->status : ''}}">{{$data->status ?? ''? $data->status : 'Select status'}} </option> -->
                        @if($data->active == '0')
                        <option value="">Select Status</option>
                        <option value="1">Active</option>
                        <option value="0" selected>Inactive</option>
                        @elseif($data->active == '1')
                        <option value="">Select Status</option>
                        <option value="1" selected>Active</option>
                        <option value="0">Inactive</option>
                        @else
                        <option value="">Select Status</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                        @endif
                    </x-adminlte-select>
                </div>
                <div class="col-6">
                    <!-- <x-adminlte-input label="Source" name="source" type="text" placeholder="source" value="{{ old('source') }}" /> -->
                    <x-adminlte-select name="source" label="Select Source:" id="source">
                        <option value="{{$data->source ?? ''? $data->source : ''}}">{{$data->source ?? ''? $data->source : 'Select Source'}} </option>
                        <option value="IN">IN</option>
                        <option value="USA">USA</option>
                        <option value="AE">AE</option>
                        <option value="KSA">KSA</option>
                    </x-adminlte-select>
                </div>

                <div class="col-6">
                    <!-- <x-adminlte-input label="Destination" name="destination" type="text" placeholder="destination" value="{{ old('destination') }}" /> -->
                    <x-adminlte-select name="destination" label="Select destination:" id="destination">
                        <option value="{{$data->destination ?? ''? $data->destination : ''}}">{{$data->destination ?? ''? $data->destination : 'Select destination'}} </option>
                        <option value="IN">IN</option>
                        <option value="USA">USA</option>
                        <option value="AE">AE</option>
                        <option value="KSA">KSA</option>
                    </x-adminlte-select>
                </div>


                <div class="col-6">
                    <x-adminlte-select name="type" label="Select type:" id="type">
                        <!-- <option value="{{$data->type ?? ''? $data->type : ''}}">{{$data->type ?? ''? $data->type : 'Select type'}} </option>
                        <option value="1">Domestic</option>
                        <option value="2">International</option>
                        <option value="3">Both</option> -->
                        @if($data->type == '1')
                        <option value="1" selected>International</option>
                        <option value="2">Domestic</option>
                        <option value="3">Both</option>
                        @elseif($data->type == '2')
                        <option value="1">International</option>
                        <option value="2" selected>Domestic</option>
                        <option value="3">Both</option>
                        @else
                        <option value="1">International</option>
                        <option value="2">Domestic</option>
                        <option value="3" selected>Both</option>
                        @endif
                    </x-adminlte-select>
                </div>

            </div>
            <div class="row justify-content-left">
                <div class="col-6">
                    <!-- <x-adminlte-input label="Time Zone" name="time_zone" type="text" placeholder="time_zone" value="{{ $data->time_zone }}" /> -->
                    <x-adminlte-select name="time_zone" label="Select Time Zone:" id="time_zone">
                        <option value="{{$data->time_zone ?? ''? $data->time_zone : ''}}">{{$data->time_zone ?? ''? $data->time_zone : 'Select TimeZone'}} </option>
                        <option value="Asia/Kolkata"> Asia/Kolkata</option>
                        <option value="Asia/Dubai">Asia/Dubai</option>
                        <option value="Asia/Kuwait">Asia/Kuwait</option>
                        <option value="Asia/Riyadh">Asia/Riyadh</option>
                        <option value="Asia/Qatar"> Asia/Qatar</option>
                        <option value="Asia/Bahrain">Asia/Bahrain </option>

                    </x-adminlte-select>
                </div>
                <div class="col-6">
                    <x-adminlte-input label="user_id" name="user_id" type="text" i="user_id" placeholder="user_id" value="{{ $data->user_id }}" />
                </div>

                <div class="col-6">
                    <x-adminlte-input label="password" name="password" type="text" id="Password" placeholder=" password" value="{{ $data->password }}" />
                </div>

                <div class="col-6">
                    <x-adminlte-input label="account_id" name="account_id" id="account_id" type="text" placeholder="account_id" value="{{ $data->account_id }}" />
                </div>

                <div class="col-6">
                    <x-adminlte-input label="key1" name="key1" type="text" placeholder="key1" value="{{ $data->key1 }}" />
                </div>

                <div class="col-6">
                    <x-adminlte-input label="key2" name="key2" type="text" placeholder="key2" value="{{ $data->key2 }}" />
                </div>
            </div>
            <div class="text-center">
                <x-adminlte-button label=" Submit" theme="primary" icon="fas fa-save" type="submit" />
            </div>
        </form>
    </div>
</div>
<div class="col"></div>
</div>

@stop


@section('js')
<script type="text/javascript">
    $(document).ready(function() {
        $('#Password,#user_id,#account_id').attr('autocomplete', 'off');
    });
</script>
@stop