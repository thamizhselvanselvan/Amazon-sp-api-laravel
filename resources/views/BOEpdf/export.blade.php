@extends('adminlte::page')

@section('title', 'BOE Export')
@section('css')

<link rel="stylesheet" href="/css/styles.css">

<!-- <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" /> -->

@stop


@section('content_header')

<h1 class="m-0 text-dark">BOE Export Filter</h1>
@stop

@section('content')

<div class="row">
    <div class="col">

        <div class="alert_display">
            @if ($message = Session::get('success'))
            <div class="alert alert-success alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $message }}</strong>
            </div>
            @endif
        </div>
    </div>
</div>

<form action="{{ route('BOE.Export.Filter') }}" method="POST" id="boe_export_filter" class="row">
@csrf
    <div class="col-3">
        <x-adminlte-select label="Company" name="company" id="company">
            @if($role == 'Admin')
            <option value="0">ALL</option>
            @endif
            @foreach ($companys as $company)
            <option value="{{ $company->id }}"> {{ $company->company_name }}</option>
            @endforeach
        </x-adminlte-select>
    </div>
    <!-- <div class="col-3">
        <x-adminlte-input label="Name Of Consignor" name="email" id="email" type="text" placeholder="" value="{{ old('email') }}" />
    </div> -->
    <div class="col-3">
        <div class="form-group">
            <label>Date Of Arrival:</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="far fa-calendar-alt"></i>
                    </span>
                </div>
                <input type="text" class="form-control float-right datepicker" name='date_of_arrival' id="date_of_arrival">
            </div>

        </div>
        <!-- <x-adminlte-input label="Date Of Arrival" name="email" id="email" type="text" placeholder="Email" value="{{ old('email') }}" /> -->
    </div>
    <!-- <div class="col-3">
        <div class="form-group">
            <label>Challan Date:</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="far fa-calendar-alt"></i>
                    </span>
                </div>
                <input type="text" class="form-control float-right datepicker" name='challan_date' id="challan_date">
            </div>

        </div>
    </div> -->
</form>
<div class="row">

    <div class="text-center">
       
            <x-adminlte-button label="Search" theme="primary" icon="fas fa-search" type="submit" />
            <x-adminlte-button label="Export" theme="primary" icon="fas fa-file-export" id='export_boe' />
    </div>
</div>
<table class="table table-bordered yajra-datatable table-striped">
    <thead>
        <tr>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>

@stop

@section('js')

<script type="text/javascript">
    $(document).ready(function($) {

        $('.datepicker').daterangepicker({
            locale: {
            format: 'DD/MM/YYYY'
        }
        });
    });
    $('#export_boe').on('click', function(e)
    {
        e.preventDefault();
        $('#boe_export_filter').submit();
    });
</script>
@stop