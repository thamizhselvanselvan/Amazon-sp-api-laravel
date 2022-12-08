@extends('adminlte::page')

@section('title', 'Order Statistics')

@section('content_header')

    <div class="row mt-3">
        <div class="col">
            <h1 class="m-0 text-dark text-center ">Order CSV Upload</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-4">
            <!-- <a href="{{ route('label.manage') }}" class="btn btn-primary btn-sm">
                                                                            <i class="fas fa-long-arrow-alt-left"></i> Back
                                                                        </a> -->

            <a href=" {{ route('download.order.csv.template') }} ">
                <x-adminlte-button label="Download Order Template" theme="primary" icon="fas fa-file-download"
                    class="btn-sm ml-2" />
            </a>

        </div>
    </div>

@stop

@section('css')
    <link rel="stylesheet" href="/css/styles.css">
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
                <x-adminlte-alert theme="success" title="Success" dismissable>
                    {{ session()->get('success') }}
                </x-adminlte-alert>
            @endif

            @if (session()->has('error'))
                <x-adminlte-alert theme="danger" title="Error" dismissable>
                    {{ session()->get('error') }}
                </x-adminlte-alert>
            @endif

            <x-adminlte-alert theme="danger" title="Error" dismissable id="alert" style="display:none">
                Please Select Order CSV.
            </x-adminlte-alert>

            <form class="row" id="multi-file-upload" method="POST" action="{{ route('import.orders.file') }}"
                accept-charset="utf-8" enctype="multipart/form-data">
                @csrf
                <div class="col-3"></div>
                <div class="col-6">

                    <x-adminlte-select name="store_name" id="store" class='store' label="Select Store">
                        <option value="0">Select Store</option>
                        @foreach ($order_sellers as $seller)
                            <option value="{{ $seller['seller_id'] }}"> {{ $seller['store_name'] }} </option>
                        @endforeach
                    </x-adminlte-select>

                    <x-adminlte-input label="Choose CSV File" name="order_csv" id="files" type="file" />
                </div>
                <div class="col-12">
                    <div class="text-center">
                        <x-adminlte-button label="Upload" theme="primary" class="add_ btn-sm" icon="fas fa-plus"
                            type="submit" id="order_upload" />
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
                url: "/orders/file/management/monitor/",
                data: {
                    "module_type": "IMPORT_ORDER",
                    "_token": "{{ csrf_token() }}",
                },
                response: 'json',
                success: function(response) {
                    // console.log(response);
                    if (response == '0000-00-00 00:00:00') {

                        $('#order_upload').prop('disabled', true);
                        $('#order_upload').attr("title", "File is importing...");
                    }

                },
            });
        });

        $('#order_upload').click(function() {
            $('#order_upload').html('<i class="fa fa-circle-o-notch fa-spin"></i> Uploading...');
        });
    </script>
@stop
