@extends('adminlte::page')
@section('title', 'Label')

@section('css')

    <link rel="stylesheet" href="/css/styles.css">
@stop

@section('content_header')
    <div class="row mb-4">
        <div class="col-1">

            <a href="{{ route('label.manage') }}">
                <x-adminlte-button label='Back' class="btn-sm" theme="primary" icon="fas fa-long-arrow-alt-left"
                    type="submit" />
            </a>

        </div>
        <div class="col">

            <h1 class=" text-dark text-center">Label Search By</h1>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-1 ">
            <input type="radio" class="order_id" name="priority" value="" checked />
            <label for="P3">Order Id</label>
        </div>
        <div class="col-1 ">
            <input type="radio" class="tracking_id" name="priority" value="">
            <label for="P3">AWB No.</label>

        </div>
    </div>
@stop

@section('content')
    @csrf

    <div class="row ">
        <div class="col search_by_order_id">
            <label>Amazon Order Id</label>
            <textarea class="form-control" rows="4" placeholder="Eg:- Amazon Order Id: 333-7777777-7777777" name="order_id"
                id='order_id'></textarea>
            <div class="text-right m-2">
                <x-adminlte-button label='Search' class="btn-sm search-amazon-order-id" theme="primary"
                    icon="fas fa-file-upload" type="submit" />
            </div>
        </div>
        <div class="col search_by_tracking_id d-none">
            <label> AWB No.</label>
            <textarea class="form-control" rows="4" placeholder="Enter Awb No." name="awb_no" id='awb_tracking_id'></textarea>
            <div class="text-right m-2">
                <x-adminlte-button label='Search' class="btn-sm search-by-awb-tracking-no" theme="primary"
                    icon="fas fa-file-upload" type="submit" />
            </div>
        </div>
    </div>

    <div id="showTable" class="d-none">
        <table class='table table-bordered table-striped text-center'>
            <thead>
                <tr class='text-bold bg-info'>
                    <!-- <th>Selected All <br><input type='checkbox' id='selectAll' /></th> -->
                    <th>Store Name</th>
                    <th>Order No.</th>
                    <th>Awb No.</th>
                    <th>Courier Name</th>
                    <th>Order Date</th>
                    <!-- <th>SKU</th> -->
                    <th>Customer</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id='checkTable'>
            </tbody>
        </table>
    </div>

    <div id="showTableMissing" class="d-none">
        <table class='table table-bordered table-striped text-center'>
            <thead>
                <tr class='text-bold bg-info'>
                    <!-- <th>Selected All <br><input type='checkbox' id='selectAll' /></th> -->
                    <th>Order No.</th>
                    <th>AWB No.</th>
                    <th>Courier Name</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id='checkTableMissing'>
            </tbody>
        </table>
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            $('.search-amazon-order-id').on('click', function() {

                $('#showTable').addClass('d-none');
                $('#showTableMissing').addClass('d-none');

                var form_data = $('.form-control').val();
                $.ajax({
                    method: 'POST',
                    url: "{{ route('lable.search.amazon-order-id') }}",
                    data: {
                        'order_id': form_data,
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#showTable').removeClass('d-none');
                            $('#checkTable').html(response.success);

                            let view = $('.view').attr('href');
                            window.open(view, '_blank');
                        }
                        if (response.missing) {
                            $('#showTableMissing').removeClass('d-none');
                            $('#checkTableMissing').html(response.missing);
                        }
                    }
                });
            });

            $(document).on('click', '.update', function() {

                let order_id = $(this).attr("ID");
                let tracking_id = $('#tracking' + order_id).val();
                let courier = $('#courier' + order_id).val();
                // alert(tracking_id);
                // alert(courier);
                $.ajax({
                    method: 'POST',
                    url: "{{ route('lable.update.tracking-details') }}",
                    data: {
                        'order_id': order_id,
                        'tracking_id': tracking_id,
                        'courier': courier,
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(response) {
                        $('.search-amazon-order-id').click();
                    }
                });
            });

            $('#order_id').on('keyup', function(e) {
                var form_data = $('.form-control').val();
                if (form_data.length == 19 && !e.ctrlKey) {

                    $('.search-amazon-order-id').click();
                }
            });
        });

        $('.order_id').click(function() {
            $('.search_by_tracking_id').addClass('d-none');
            $('.search_by_order_id').removeClass('d-none');
        });

        $('.tracking_id').click(function() {
            $('.search_by_order_id').addClass('d-none');
            $('.search_by_tracking_id').removeClass('d-none');
        });

        $('.search-by-awb-tracking-no').click(function() {
            let awb_no = $('#awb_tracking_id').val();
            if (awb_no == '') {
                alert('Please Enter Awb No.');
            } else {

                $.ajax({
                    method: 'POST',
                    url: "{{ route('lable.search.by.awb.no') }}",
                    data: {
                        'awb_no': awb_no,
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(response) {
                        // console.log(response);
                        $('#showTableMissing').addClass('d-none')
                        $('#showTable').removeClass('d-none');

                        let record_of_awb_id = '';
                        $.each(response, function(key, result) {

                            record_of_awb_id += "<tr><td>" + result['store_name'] +
                                "</td><td>" + result['order_no'] + "</td><td>" + result[
                                    'awb_no'] + "</td><td>" + result['forwarder'] +
                                "</td><td>" + moment(result[
                                    'purchase_date']).utc().format('YYYY-MM-DD') +
                                "</td><td>" +
                                JSON
                                .parse(result['shipping_address']).Name +
                                "</td><td><a href='/label/pdf-template/" + result['id'] +
                                "' class='btn-sm btn-success' target='_blank'><i class='fas fa-eye'></i>View</a><a href='/label/download-direct/" +
                                result['id'] +
                                "' class='btn-sm btn-info ml-1'><i class='fas fa-download'></i>Download</a></td>";

                        });
                        $('#checkTable').html(record_of_awb_id);
                    }
                });
            }

        });
    </script>
@stop
