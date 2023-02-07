@extends('adminlte::page')

@section('title', 'Admin Password Reset')

@section('css')

<link rel="stylesheet" href="/css/styles.css">

@stop

@section('content_header')

    <div class="row">
        <div class="col">
            <a href="{{route('admin.user_list')}}" class="btn btn-primary">

                <i class="fas fa-long-arrow-alt-left"></i> Back
            </a>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col">
            <h1 class="m-0 text-dark text-center">Admin Reset Password</h1>
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

        @if(session()->has('success'))
            <div class="alert alert-success">
                {{ session()->get('success') }}
            </div>
        @endif
  
        <form action="{{Route ('admin.password_reset_save', $user_id)}}" method="POST" id="admin_adminpassword">
            @csrf

            <div class="row">
                <div class="col-6">
                    <x-adminlte-input label="Password" name="password" id="password" type="password" placeholder="Password"/>
                    <span toggle="#password" class="eye-icon fa fa-fw fa-eye toggle-password"></span>
                </div>
                <div class="col-6">
                    <x-adminlte-input label="Password Confirmation" name="password_confirmation" id="password_confirmation" type="password" placeholder="Password Confirmation"/>
                    <span toggle="#password_confirmation" class="eye-icon fa fa-fw fa-eye toggle-password"></span>
                </div>
            </div>
            
            <div class="text-center">
                <x-adminlte-button label="Reset Password" theme="primary" class="reset_password" icon="fas fa-save" type="submit" />
            </div>
        </form>
    </div>
    <div class="col"></div>
</div>

@stop


@section('js')

    <script>
        $(document).on('click', ".reset_password", function(e) {
            e.preventDefault();

            let bool = confirm("Are you sure you want to change password ?");

            if(!bool) {
                return false;
            }

            let self = $(this);
            let form = $("#admin_adminpassword");

            form.submit();

        });

        $(".toggle-password").click(function() {

            $(this).toggleClass("fa-eye fa-eye-slash");

            var input = $($(this).attr("toggle"));

            if (input.attr("type") == "password") {
                input.attr("type", "text");
            } else {
                input.attr("type", "password");
            }
            
        });

        $(document).ready(function($) {
            $('#password').passtrength({
                minChars: 8,
                tooltip: true,
                textWeak: "Weak",
                textMedium: "Medium",
                textStrong: "Strong",
                textVeryStrong: "Very Strong",
                passwordToggle: false,
            });
        });

    </script>

@stop

   



