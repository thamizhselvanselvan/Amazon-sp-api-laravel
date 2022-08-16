@extends('adminlte::page')

@section('title', 'Export')

@section('content_header')

<div class="row">
    <h1 class="m-0 text-dark">Forwarder Missing Export Filter</h1>
</div>
<div class="col">
    <div style="margin-top: 1.8rem;">
        <a href="{{route('shipntrack.forwarder')}}" class="btn-sm btn btn-primary">
            <i class="fas fa-long-arrow-alt-left"></i> Back
        </a>
        <x-adminlte-button label="Export" theme="primary" icon="fas fa-file-export" id='export_missing' class="btn-sm expoer_missing" />
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

        @if(session()->has('success'))
        <x-adminlte-alert theme="success" title="Success" dismissable>
            {{ session()->get('success') }}
        </x-adminlte-alert>
        @endif

        @if(session()->has('error'))
        <x-adminlte-alert theme="danger" title="Error" dismissable>
            {{ session()->get('error') }}
        </x-adminlte-alert>
        @endif
    </div>

</div>
</div>
<div class="row">
    <div class="col-2">
        <div class="form-group">
            <label>Order Date:</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="far fa-calendar-alt"></i>
                    </span>
                </div>
                <input type="text" class="form-control float-right datepicker" name='date_of_arrival' autocomplete="off" id="date_of_arrival">
            </div>

        </div>
    </div>
    <div class="col-2">
        <div style="margin-top:2.2rem;">
            <input type="checkbox" name="first" id="first" value='forwarder_1'>
            <label for=" entire"> First forwarder Missing</label>
        </div>
    </div>
    <div class="col-2">
        <div style="margin-top:2.2rem;">
            <input type="checkbox" name="second" id="second" value='forwarder_2'>
            <label for="ware"> Second forwarder Missing</label>
        </div>
    </div>

</div>


@stop
@section('js')
<script type="text/javascript">
    $(document).ready(function($) {
        $("#report_table").hide();
        $('.datepicker').daterangepicker({
            autoUpdateInput: false,
            locale: {
                format: 'YYYY-MM-DD',
            },
        });
        $('.datepicker').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
        });

        $('.datepicker').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });

    });

    $('#export_missing').on('click', function() {

        let date_of_arrival = $('#date_of_arrival').val();
        let first_forwarder = $('#first').is(':checked');
        let second_forwarder = $('#second').is(':checked');
        let selected_details = '';


        selected_details = date_of_arrival + '!=' + first_forwarder + '!=' + second_forwarder;

        $.ajax({
            method: 'get',
            url: '/shipntrack/missing/export',
            data: {
                "_token": "{{ csrf_token() }}",
                'selected': selected_details,
            },
            success: function(response) {
                window.location.href = '/shipntrack/missing/download';
                alert('Downloaded successfully');
            },
            error: function(response) {
                alert('Error');

            }
        })
    });
</script>
@stop