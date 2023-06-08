@extends('adminlte::page')

@section('title', 'Missing Price')

@section('css')

    <link rel="stylesheet" href="/css/styles.css">
    <style>
        .table td {
            padding: 0;
            padding-left: 5px;
        }

        .table th {
            padding: 2;
            padding-left: 5px;
        }
    </style>
@stop

@section('content_header')
    <h1 class="m-0 text-dark">B2CShip Orders Missing Details</h1>

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
                @if ($message = Session::get('error'))
                    <div class="alert alert-danger alert-block">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        <strong>{{ $message }}</strong>
                    </div>
                @endif
            </div>
            <div class="alert_display">
                @if (request('success'))
                    <div class="alert alert-success alert-block">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        <strong>{{ request('success') }}</strong>
                    </div>
                @endif
            </div>
            <div class="alert_display">
                @if (request('error'))
                    <div class="alert alert-danger alert-block">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        <strong>{{ request('error') }}</strong>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="modal fade" id="price_missing" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="new_asin">Update Missing Details :</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- <form class="row" id="multi-file-upload" method="POST" action="" accept-charset="utf-8" enctype="multipart/form-data">
                                                @csrf -->
                    <div class="col-12">
                        <div>
                            <h5> <b> ASIN : </b><a name="asin" id="asin"></a> </h5>
                            <h5> <b>Amazon Order ID : </b><a name="order_id" id="order_id"></a> </h5>
                            <h5 hidden="hidden"> <b>Order Item ID : </b><a name="order_item_id" id="order_item_id"></a>
                            </h5>
                        </div>
                    </div>
                    <div class="col-11">
                        <x-adminlte-input label="Name" name="Name" id="Name" type="text"
                            placeholder="Enter Name..." />
                    </div>
                    <div class="col-11">
                        <x-adminlte-input label="AddressLine1" name="AddressLine1" id="AddressLine1" type="text"
                            placeholder="Enter AddressLine1..." />
                    </div>
                    <div class="col-11">
                        <x-adminlte-input label="AddressLine2" name="AddressLine2" id="AddressLine2" type="text"
                            placeholder="Enter AddressLine2..." />
                    </div>
                    <div class="col-11">
                        <x-adminlte-input label="City" name="City" type="text" id="City"
                            placeholder="Enter City..." />
                    </div>
                    <div class="col-11">
                        <x-adminlte-input label="StateOrRegion" name="StateOrRegion" id="StateOrRegion" type="text"
                            placeholder="Enter StateOrRegion..."></x-adminlte-input>
                    </div>
                    <div class="col-11">
                        <x-adminlte-input label="PostalCode" name="PostalCode" id="PostalCode" type="text"
                            placeholder="Enter PostalCode..." />
                    </div>
                    <div class="col-11">
                        <x-adminlte-input label="CountryCode" name="CountryCode" id="CountryCode" type="text"
                            placeholder="Enter CountryCode..." />
                    </div>
                    <div class="col-11">
                        <x-adminlte-input label="Phone" name="Phone" id="Phone" type="text"
                            placeholder="Enter Phone..." />
                    </div>
                    <div class="col-11">
                        <x-adminlte-input label="AddressType" name="AddressType" id="AddressType" type="text"
                            placeholder="Enter AddressType..." />
                    </div>
                    <div class="col-12">
                        <x-adminlte-input label="Enter US Price" name="US_price" id="US_price" type="text"
                            placeholder="Enter US Price..." />
                    </div>
                    <div class="col">

                        <x-adminlte-button label="Update" theme="primary" class="add_ btn-sm" icon="fas fa-upload"
                            type="submit" id="price_upload" />
                        <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal" id="close"><i
                                class="fas fa-window-close" aria-hidden="true"></i> Close</button>
                    </div>
                    <!-- </form> -->
                </div>
            </div>
        </div>
    </div>

    <table class="table table-bordered yajra-datatable table-striped">
        <thead>
            <tr class="table-info">
                <th>ID</th>
                <th>Title</th>
                <th>ASIN</th>
                <th>Amazon Order ID</th>
                <th>Price</th>
                <th>Missing Details</th>
                <th>Status</th>
                <th>Update</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>

@stop


@section('js')
    <script type="text/javascript">
        $(function() {
            $(document).ready(function() {
                $('[data-toggle="tooltip"]').tooltip();
            });

            $(document).on('click', '.copy_clipboard', function() {
                navigator.clipboard.writeText($(this).attr('value'));
            });

            // $(document).on('click', '#asin', function() {
            //     data = $(this).attr('value');
            //     navigator.clipboard.writeText(data);
            // });
            // $(document).on('click', '#order_id', function() {
            //     data = $(this).attr('value');
            //     navigator.clipboard.writeText(data);
            // });

            $.extend($.fn.dataTable.defaults, {
                pageLength: 50,
            });

            let yajra_table = $('.yajra-datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('orders.usprice.missing') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'title',
                        name: 'title'
                    },
                    {
                        data: 'asin',
                        name: 'asin'
                    },
                    {
                        data: 'amazon_order_id',
                        name: 'amazon_order_id'
                    },
                    {
                        data: 'price',
                        name: 'price'
                    },
                    {
                        data: 'missing_details',
                        name: 'missing_details'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },

                    {
                        data: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

            $(document).on('click', '#price_update', function() {

                //  let order_data = $(this).attr('value');
                // $idea = explode('-', $request -> id);

                let asin = $(this).attr('data-asin');
                let order_id = $(this).attr('data-order-id');
                let order_item_id = $(this).attr('data-order-item-id');
                let price = $(this).attr('data-price');
                let address = $(this).attr('data-missing-details');
                let data = address.replace(/_/g, ' ');

                if (data !== "not missing") {
                    let json = JSON.parse(data);
                    // Iterate over the properties of the data object
                    $.each(json, function(key, value) {
                        // Check if the input field exists before setting its value
                        if ($('input[name="' + key + '"]').length) {
                            $('input[name="' + key + '"]').val(value);
                        }

                    });
                }

                $('#price_missing').modal('show');

                $(".modal-body #asin").text(asin);
                $(".modal-body #order_id").text(order_id);
                $(".modal-body #order_item_id").text(order_item_id);

                if (data === "not missing") {
                    $(".col-11, #Name, #AddressLine1, #AddressLine2, #City, #StateOrRegion, #PostalCode, #CountryCode, #Phone, #AddressType,  label[for='Name'], label[for='AddressLine1'], label[for='AddressLine2'], label[for='City'], label[for='StateOrRegion'], label[for='PostalCode'], label[for='CountryCode'], label[for='Phone'], label[for='AddressType']")
                        .css("display", "none");
                } else {
                    $(".col-11, #Name, #AddressLine1, #AddressLine2, #City, #StateOrRegion, #PostalCode, #CountryCode, #Phone, #AddressType, label[for='Name'], label[for='AddressLine1'], label[for='AddressLine2'], label[for='City'], label[for='StateOrRegion'], label[for='PostalCode'], label[for='CountryCode'], label[for='Phone'], label[for='AddressType']")
                        .css("display", "");
                }

                if (price == "price_found") {
                    $("#US_price, label[for='US_price']").css("display", "none");
                } else {
                    $("#US_price, label[for='US_price']").css("display", "");
                }

                if (price == "price_not_found") {
                    price = '';
                }
                $('input[name="US_price"]').val(price);


                // let data = order_data.split("_");
                // let asin = data['0'];
                // let order_id = data['1'];
                // let item_id = data['2'];
                // let country_code = data['3'];

                // $('#price_missing').modal('show');
                // $(".modal-body #asin").text(asin);
                // $(".modal-body #order_id").text(order_id);
                // $(".modal-body #order_item_id").text(item_id);
                // $(".modal-body #country_code").text(country_code);

            });

        });

        $('#close').click(function() {
            $('#price_missing').modal('hide');
        });

        $('#price_upload').click(function() {

            $(this).prop('disabled', true);

            let asin = $('#asin').text();
            let order_id = $('#order_id').text();
            let item_id = $('#order_item_id').text();
            let price = $('#US_price').val();
            let Name = $('#Name').val();
            let AddressLine1 = $('#AddressLine1').val();
            let AddressLine2 = $('#AddressLine2').val();
            let City = $('#City').val();
            let StateOrRegion = $('#StateOrRegion').val();
            let PostalCode = $('#PostalCode').val();
            let CountryCode = $('#CountryCode').val();
            let Phone = $('#Phone').val();
            let AddressType = $('#AddressType').val();

            if (price == '') {
                alert('Please Enter Price');
                $('#price_upload').prop('disabled', false);
                return false;
            }

            // let data = [asin, order_id, item_id, price, country_code];

            $.ajax({
                method: 'post',
                url: "{{ route('orders.price.us.update') }}",
                data: {
                    'asin': asin,
                    'order_id': order_id,
                    'item_id': item_id,
                    'price': price,
                    'Name': Name,
                    'AddressLine1': AddressLine1,
                    'AddressLine2': AddressLine2,
                    'City': City,
                    'StateOrRegion': StateOrRegion,
                    'PostalCode': PostalCode,
                    'CountryCode': CountryCode,
                    'Phone': Phone,
                    'AddressType': AddressType,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(response) {

                    $('#price_upload').prop('disabled', false);
                    console.log(response['error']);
                    if (response['data'] == 'success') {
                        window.location.href =
                            '/orders/b2cship/missing?success=Missing Details updated successfully'
                    } else if (response['data'] == 'error') {
                        window.location.href =
                            '/orders/b2cship/missing?error=Missing Details Not Updated(cheack Order ID, Order Item ID and Lead ID)'
                    } else {
                        alert('success');
                        window.location.reload();
                    }

                },
                error: function(response) {
                    $('#price_upload').prop('disabled', false);
                    alert('something went wrong Please Contact Admin');
                }
            });
        });
    </script>
@stop
