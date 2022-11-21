@extends('adminlte::page')

@section('title', 'Invoice Import')


@section('content_header')

{{-- <div class="row mt-0">
        <div class="col">
            <h1 class="m-0 text-dark text-left ">Invoice Management</h1>
        </div>
    </div> --}}
<div class="row">
    <div class="col mt-2">
        <a href="{{ route('invoice.index') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-long-arrow-alt-left"></i> Back
        </a>
    </div>
    <div class="col">
        <h1 class="mt-2 text-center">Invoice Import</h1>
    </div>
    <div class="col"></div>
</div>

@stop

@section('css')

@stop

@section('content')



<div class="loader d-none">
    <div class="sub-loader position-relative ">
        <div class="lds-hourglass"></div>
        <p>Loading...</p>
    </div>
</div>

<div class="row">
    <div class="col">
        <div class="alert alert-warning alert-block info-msg d-none">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <strong id='info-value'></strong>
        </div>
    </div>
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

        <form class="row" action="{{ route('invoice.csv.file.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="col-3"></div>
            <div class="col-6">
                <x-adminlte-input label="Choose File" name="invoice_csv" id="asin" type="file" />
            </div>

            <div class="col-3"></div>

            <div class="col-12">
                <div class="text-center">
                    <x-adminlte-button label="Upload" theme="primary" icon="fas fa-plus" type="submit" id="invoice_upload_button" />
                </div>
            </div>
        </form>

    </div>
    <div class="col"></div>

</div>

@stop

@section('js')
<script>
    $(document).ready(function() {
        $.ajax({
            method: 'get',
            url: "/invoice/file/management/monitor/",
            data: {
                "module_type": "IMPORT_INVOICE",
                "_token": "{{ csrf_token() }}",
            },
            response: 'json',
            success: function(response) {

                console.log(response);
                if (response.status == 'Processing') {
                    $('#invoice_upload_button').prop('disabled', true);

                    $('.info-msg').removeClass('d-none');
                    $('#info-value').html(response.description);

                } else if (response.description) {

                    $('.info-msg').removeClass('d-none');
                    $('#info-value').html(response.description);
                }
            },
        });
    });

    $('#invoice_upload_button').click(function() {
        $('#invoice_upload_button').html('<i class="fa fa-circle-o-notch fa-spin"></i> Uploading...');

    });
</script>
@stop