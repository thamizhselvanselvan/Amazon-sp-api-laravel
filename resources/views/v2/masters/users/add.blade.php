@extends('adminlte::page')

@section('title', 'Add user')

@section('css')
<link rel="stylesheet" href="/css/styles.css">
@stop

@section('content_header')
<style>
    .role-section{
    display: grid;
    grid-template-columns: 1fr 1fr;
}
.input-align{
    display: flex;
    align-items: center;
}
</style>
<div class="row">
    <div class="col">
        <a href="{{ Route('users.home') }}" class="btn btn-primary">
            <i class="fas fa-long-arrow-alt-left"></i> Back
        </a>
    </div>
</div>

<div class="row mt-3">
    <div class="col">
        <h1 class="m-0 text-dark text-center">Add User</h1>
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

        <form action="{{ route('users.home') }}" method="POST" id="admin_user">


            @csrf

            <div class="row">
                <div class="col-6">
                    <x-adminlte-input label="Name" name="name" id="name" type="text" placeholder="Name" value="{{ old('name') }}" />
                </div>
                <div class="col-6">
                    <x-adminlte-input label="Email" name="email" id="email" type="text" placeholder="Email" value="{{ old('email') }}" />
                </div>
            </div>

            <div class="row">
                <div class="col-6">
                    <x-adminlte-input label="Password" name="password" id="password" type="password" placeholder="Password" value="{{ old('password') }}" />
                </div>
                <div class="col-6">
                    <x-adminlte-input label="Password Confirmation" name="password_confirmation" id="password_confirmation" type="password" placeholder="Password Confirmation" value="{{ old('password_confirmation') }}" />
                </div>
            </div>
            <div class="row">
                <div class="col-6">

                    <label for="role">
                        Role
                    </label>
                    <div class="form-check role-section">
                        @foreach ($roles as $role)
                        <div class="input-align">
                            <input type="checkbox" id="role[]" value="{{ $role->name }}" name="role[]" >
                            <label class="form-check-label" for="role"> {{ $role->name }} </label>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="col-6">
                    <label for="department">Department</label>
                    <div class="form-check">
                        @foreach ($departments as $department)
                        <input type="radio" id="department" name="department" value="{{ $department->id }}">
                        <label class="form-check-label" for="department"> {{ $department->department }}</label>
                        @endforeach
                    </div>

                </div>
            </div>
            <div class="row">
                <div class="col-6">

                    <x-adminlte-select name="company[]" id="company[]" label="Company" multiple>
                        <option value=""> --Select-- </option>
                        @foreach ($companys as $company)
                        <option value="{{ $company->id }}"> {{ $company->company_name }}</option>
                        @endforeach
                    </x-adminlte-select>
                </div>

            </div>


            <div class="text-center">
                <x-adminlte-button label="Add User" theme="primary" icon="fas fa-plus" type="submit" />
            </div>
        </form>
    </div>
    <div class="col"></div>
</div>

@stop

