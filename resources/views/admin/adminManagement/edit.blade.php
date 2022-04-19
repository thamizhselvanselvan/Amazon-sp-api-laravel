@extends('adminlte::page')

@section('title', 'Edit user')

@section('css')
    <link rel="stylesheet" href="/css/styles.css">
@stop

@section('content_header')

    <div class="row">
        <div class="col">
            <a href="{{ Route('admin.user_list') }}" class="btn btn-primary">
                <i class="fas fa-long-arrow-alt-left"></i> Back
            </a>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col">
            <h1 class="m-0 text-dark text-center">Edit User</h1>
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

            <form action="{{ route('admin.update_user', $user_id) }}" method="POST" id="admin_user">


                @csrf

                <div class="row">
                    <div class="col-6">
                        <x-adminlte-input label="Name" name="name" id="name" type="text" placeholder="Name"
                            value="{{$user_name }}" />
                    </div>
                    <div class="col-6">
                        <x-adminlte-input label="Email" name="email" id="email" type="text" placeholder="Email"
                            value="{{ $user_email}}" />
                    </div>
                </div>

                <div class="row">
                    <div class="col-6">

                        <x-adminlte-select name="Role" id="status" label="Role">
                            @foreach ($roles as $role)
                                @if($role->name == $selected_roles)
                                     <option value="{{ $role->name }}" selected> {{ $role->name }}</option>
                                @else
                                    <option value="{{ $role->name }}"> {{ $role->name }}</option>
                                @endif
                            @endforeach
                        </x-adminlte-select>
                    </div>
                    <div class="col-6">

                        <x-adminlte-select name="company" id="company" label="Company">
                            @foreach ($companys as $company)
                                @if($company ->id == $selected_company)
                                <option value="{{ $company->id }}" selected> {{ $company->company_name }}</option>
                                @else
                                <option value="{{ $company->id }}"> {{ $company->company_name }}</option>
                                @endif
                            @endforeach
                        </x-adminlte-select>
                    </div>

                </div>



                <div class="text-center">
                    <x-adminlte-button label="Edit User" theme="primary" icon="fas fa-plus" type="submit" />
                </div>
            </form>
        </div>
        <div class="col"></div>
    </div>

@stop
