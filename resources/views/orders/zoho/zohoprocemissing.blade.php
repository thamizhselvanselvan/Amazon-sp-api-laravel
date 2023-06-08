@extends('adminlte::page')

@section('title', 'Missing Order')

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

        .wrong {
            color: red;
        }

        .click {
            color: green;
        }
    </style>

@stop

@section('content_header')
<h1 class="m-0 text-dark">Price Missing Price Details</h1>

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
            @if($message = Session::get('error'))
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
                <strong>{{request('success')}}</strong>
            </div>
            @endif
        </div>
        <div class="alert_display">
            @if (request('error'))
            <div class="alert alert-danger alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{request('error')}}</strong>
            </div>
            @endif
        </div>
    </div>
</div>

<div class="modal fade" id="price_missing" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="new_asin">Update Price</h5>
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
                        <h5> <b>Order Item ID : </b><a name="order_item_id" id="order_item_id"></a> </h5>
                        <h5 hidden="hidden"> <b>Country Code : </b><a name="country_code" id="country_code"></a> </h5>

                    </div>
                </div>
                <div class="col-12">
                    <x-adminlte-input label="Enter Price" name="price" type="text" placeholder="Enter Price..." />
                    <span class="d-none">
                        <x-adminlte-input label="Enter Name" name="name" type="text" placeholder="Enter Name..." class="name " />
                    </span>
                    <span class="d-none">
                        <x-adminlte-input label="Enter Address 1" name="address1" type="text" placeholder="Enter Address 1..." class="address1" />
                    </span>
                    <span class="d-none">
                        <x-adminlte-input label="Enter Address 2" name="address2" type="text" placeholder="Enter Address 2..." class="address2" />
                    </span>
            
                </div>
                
                <div class="col">

                    <x-adminlte-button label="Update" theme="primary" class="add_ btn-sm" icon="fas fa-upload" type="submit" id="price_upload" />
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal" id="close"><i class="fas fa-window-close" aria-hidden="true"></i> Close</button>
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
            <th>Country Code</th>
            <th>Title</th>
            <th>ASIN</th>
            <th>Amazon Order ID</th>
            <th>Order Item ID</th>
            <th>Price</th>
            <th>Missing Parameters</th>
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
            // $(document).on('click', '#asin', function() {
            //     data = $(this).attr('value');
            //     navigator.clipboard.writeText(data);
            // });
            // $(document).on('click', '#order_id', function() {
            //     data = $(this).attr('value');
            //     navigator.clipboard.writeText(data);
            // });
            // $(document).on('click', '#order_item', function() {
            //     data = $(this).attr('value');
            //     navigator.clipboard.writeText(data);
            // });

            $(document).on('click', '.copy_clipboard', function() {
                navigator.clipboard.writeText($(this).eq(0).attr('value'));
            });

            $.extend($.fn.dataTable.defaults, {
                pageLength: 50,
            });

            let yajra_table = $('.yajra-datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('orders.missing') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'country_code',
                        name: 'country_code'
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
                        data: 'order_item_id',
                        name: 'order_item_id'
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

                let asin = $(this).attr('data-asin');
                let order_id = $(this).attr('data-order-id');
                let order_item_id = $(this).attr('data-order-item-id');
                let country_code = $(this).attr('data-country-code');
                let name = $(this).attr('data-name');
                let adress1 = $(this).attr('data-address1');
                let adress2 = $(this).attr('data-address2');

                if(name) {
                    $(".name").removeClass("d-none");
                }

                if(adress1) {
                    $(".adress1").removeClass("d-none");
                }

                if(adress1) {
                    $(".adress2").removeClass("d-none");
                }

                $('#price_missing').modal('show');

                $(".modal-body #asin").text(asin);
                $(".modal-body #order_id").text(order_id);
                $(".modal-body #order_item_id").text(order_item_id);
                $(".modal-body #country_code").text(country_code);
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
            let country_code = $('#country_code').text();
            let price = $('#price').val();
            let name = $('.name').val();
            let adress1 = $('.adress1').val();
            let adress2 = $('.adress2').val();

            if (price == '') {
                alert('Please Enter Price');
                $('#price_upload').prop('disabled', false);
                return false;
            }

            $.ajax({
                method: 'post',
                url: "{{ route('orders.missing.price.update') }}",
                data: {
                    'asin': asin,
                    'order_id': order_id,
                    'item_id': item_id,
                    'country_code': country_code,
                    'price': price,
                    'name': name,
                    'adress1': adress1,
                    'adress2': adress2,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(response) {
                    $('#price_upload').prop('disabled', false);

                    console.log(response['error']);
                    if (response['data'] == 'success') {
                        window.location.href = '/orders/missing/price?success=Price updated successfully'
                    } else if (response['data'] == 'error') {
                        window.location.href = '/orders/missing/price?error=Price Not Updated(cheack Order ID, Order Item ID and Lead ID)'
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