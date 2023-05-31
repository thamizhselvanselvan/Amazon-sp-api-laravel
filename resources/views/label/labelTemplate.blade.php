@extends('adminlte::page')
@section('title', 'Label')

@section('content_header')
    <div class="label-company text-inverse f-w-600">
        <span class="pull-right hidden-print">
            {{-- <a href="javascript:void(0);" class="btn btn-sm btn-white m-b-10 p-l-5" id="Export_to_pdf"><i
                    class="fa fa-file t-plus-1 text-danger fa-fw fa-lg"></i> Export as PDF</a> --}}
            <a href="javascript:;" onclick="window.print()" class="btn btn-sm btn-white m-b-10 p-l-5"><i
                    class="fa fa-print t-plus-1 fa-fw fa-lg"></i> Print</a>

        </span>
        <br>
    </div>
@stop

@section('css')

    <style type="text/css">
        body div strong {
            font-family: "Lato";
            font-weight: 900;
            font-size: 12px;
        }

        body * {
            font-family: "Lato";
            font-weight: 700;
            font-size: 14px;
        }

        .table_border th,
        .table_border td,
        .table_border td,
        .table_border thead th,
        .return {
            border: 1px solid black !important;
        }

        .mb-1,
        .my-1 {
            margin-bottom: 0px !important;
        }

        .mt-1,
        .my-1 {
            margin-top: 0px !important;
        }

        .return,
        .prduct-details thead tr th {
            border-top: 0px !important;
        }

        @media print {

            @page {
                size: 4in 6in;
                margin: 0px;
                padding: 0px;
            }

            .table_border th,
            .table_border td,
            .table_border tr,
            .table_border thead th,
            .return {
                border: 1px solid black !important;
            }

            .return,
            .prduct-details thead tr th {
                border-top: 0px !important;
            }

            .container-fluid {
                size: 4in 6in;
                width: 384px;
                height: 576px;
                margin: 0px;
                padding: 0px;
            }

            #label-container {
                margin: 0px;
                padding: 0px;
                /* padding-top: 5px; */
                transform-origin: 0 0;
                transform: scale(1.4);
            }

            #label-container .label {
                margin: 0px;
                padding: 0px;
            }
        }

        .ArToEn {
            font-size: 15px;
            line-height: 14px;
            margin: 0;
            margin-top: 5px;
        }

        .address {
            margin-top: 0;
            margin-bottom: 0;
            line-height: 15px;
            padding: 5px;
        }
    </style>
@stop

@section('content')
    <input type="hidden" id="awb_no" value="{{ $awb_no }}">
    <input type="hidden" id="bag_no" value="{{ $bag_no }}">

    <div class="col-md-12" id="label-container">
        <div class="label p-1">
            <div class="label-content">
                <!-- <div class="table-responsive"> -->
                <table class="table table-label table-bordered table-bordered-dark<td pt-1 pb-0 mb-1 table_border">
                    <tbody>
                        <tr>
                            <td class="pt-0 pb-0">
                                <div class="row">
                                    <div class="col p-0">

                                    </div>
                                    <div class="col p-0">
                                        <div class="text-center">
                                            @if ($forwarder == '' || $forwarder == null)
                                                &nbsp;
                                            @else
                                                {{ $forwarder }}
                                            @endif

                                        </div>
                                        <img class="label-barcode-img" src='data:image/png;base64,{!! $bar_code !!}'>
                                        <b>
                                            <div class="text-center">
                                                @if ($result->awb_no == '' || $result->awb_no == null)
                                                    AWB is missing
                                                @else
                                                    {{ $result->awb_no }}
                                                @endif
                                            </div>
                                        </b>
                                    </div>
                                    <div class="col p-0">
                                        @if (isset($result->shipping_address['CountryCode']) && $result->shipping_address['CountryCode'] != 'AE')
                                            INTL
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="pt-1 pb-1">
                                <div class="row ">
                                    <div class="col p-0">
                                        <div class="text-inverse m-b-5 text-left"><strong>
                                                Invoice No: </strong> {{ $result->order_no }}
                                        </div>
                                        <div class="text-inverse m-b-5 text-left"><strong>
                                                Store Name: </strong> {{ $result->store_name }}
                                        </div>
                                        <div class="text-inverse m-b-5 text-left"><strong>
                                                Order Date: </strong>{{ date('Y-m-d', strtotime($result->purchase_date)) }}
                                        </div>
                                    </div>
                                </div>

                            </td>
                        </tr>
                        <tr>
                            <td class="pt-1 pb-1">
                                <div class="row">
                                    <div class="col p-0">

                                        <strong>Ship To: </strong>
                                        <strong>
                                            @if (isset($result->shipping_address['Name']))
                                                {{ $result->shipping_address['Name'] }}
                                            @endif
                                        </strong><br>

                                        <strong>Address: </strong>
                                        @if (isset($result->shipping_address['AddressLine1']))
                                            {{ $result->shipping_address['AddressLine1'] }},
                                        @endif

                                        @if (isset($result->shipping_address['AddressLine2']))
                                            {{ $result->shipping_address['AddressLine2'] }}
                                        @endif
                                        <br>
                                        <strong>City: </strong>
                                        @if (isset($result->shipping_address['City']))
                                            {{ $result->shipping_address['City'] }}
                                        @else
                                            NA
                                        @endif
                                        <br>
                                        @if (isset($result->shipping_address['County']))
                                            <strong>County: </strong>
                                            {{ $result->shipping_address['County'] }}
                                        @endif
                                        <br>
                                        @if (isset($result->shipping_address['country']))
                                            <strong>Country: </strong>
                                            {{ $result->shipping_address['country'] }}
                                        @endif
                                        <br>
                                        @if (isset($result->shipping_address['Phone']))
                                            <strong>Phone: </strong>
                                            {{ $result->shipping_address['Phone'] }}
                                        @endif
                                        <br>
                                        @if (isset($getTranslatedText[0]['name']) ||
                                                isset($getTranslatedText[0]['addressline1']) ||
                                                isset($getTranslatedText[0]['addressline2']) ||
                                                isset($getTranslatedText[0]['city']) ||
                                                isset($getTranslatedText[0]['county']))
                                            <p class="ArToEn"><strong>Delivery Address:</strong>
                                                {{ $getTranslatedText[0]['name'] == null ? $result->shipping_address['Name'] : $getTranslatedText[0]['name'] }},
                                                {{ $getTranslatedText[0]['addressline1'] == null ? $result->shipping_address['AddressLine1'] ?? '' : $getTranslatedText[0]['addressline1'] }},
                                                {{ $getTranslatedText[0]['addressline2'] == null ? $result->shipping_address['AddressLine2'] ?? '' : $getTranslatedText[0]['addressline2'] }},
                                                {{ $getTranslatedText[0]['city'] == null ? $result->shipping_address['City'] : $getTranslatedText[0]['city'] }},
                                                {{ $getTranslatedText[0]['county'] == null ? $result->shipping_address['County'] : $getTranslatedText[0]['county'] }}
                                        @endif

                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table class="table table-bordered table-bordered-dark product pt-0 mb-0 prduct-details table_border">
                    <thead>
                        <tr>
                            <th class="text-left">Sr</th>
                            <th class="text-center">Product Name</th>
                            <th class="text-center">SKU</th>
                            <th class="text-center">QTY</th>
                            <!-- <th class="text-center">Price</th> -->
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($result->product as $key => $value)
                            <tr>
                                <td class="text-center p-1">{{ $key + 1 }}</td>
                                <td class="p-1">
                                    @php
                                        $new_word = wordwrap($value['title'], 20, "\n", true);
                                        echo "$new_word\n";
                                    @endphp
                                </td>
                                <td class="text-center p-1">{{ $value['sku'] }}</td>
                                <td class="text-center p-1">{{ $value['qty'] }}</td>

                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>

                <div class=" small return">

                    <p class="address"><strong>Return Address:</strong>
                        Warehouse 61, Al Habtoor Warehouses, Industrial Area 3, Al Qusias, Dubai UAE
                    </p>

                </div>
            </div>


        </div>

    @stop

    @section('js')
        <script>
            $(document).ready(function() {
                $('#Export_to_pdf').click(function(e) {
                    e.preventDefault();
                    var url = $(location).attr('href');
                    var awb_no = $('#awb_no').val();
                    var bag_no = $('#bag_no').val();
                    // alert(url);
                    $.ajax({
                        method: 'POST',
                        // url: "{{ url('/label/export-pdf') }}",
                        url: "{{ route('export.label.pdf') }}",
                        data: {
                            'url': url,
                            'awb_no': awb_no,
                            'bag_no': bag_no,
                            "_token": "{{ csrf_token() }}",
                        },
                        success: function(response) {

                            window.location.href = '/label/download/' + bag_no + '/' + awb_no;
                            alert('Download pdf successfully');
                        }
                    });
                });
            });
        </script>
    @stop
