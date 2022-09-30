@extends('adminlte::page')
@section('title', 'Label')

@section('css')


<link rel="stylesheet" href="/css/styles.css">
@stop

@section('content_header')
<div class="row">
    <a href="{{route('label.manage')}}">
        <x-adminlte-button label='Back' class="btn-sm" theme="primary" icon="fas fa-long-arrow-alt-left" type="submit" />
    </a>
    <h1 class="m-0 text-dark col-3">Label Search By Order Id</h1>
</div>
@stop

@section('content')

@csrf
<div class="row">
    <div class="col">
        <label>Amazon Order Id</label>
        <textarea class="form-control" rows="3" placeholder="Eg:- Amazon Order Id: 333-7777777-7777777" name="order_id"></textarea>
        <div class="text-right m-2">
            <x-adminlte-button label='Search' class="btn-sm search-amazon-order-id" theme="primary" icon="fas fa-file-upload" type="submit" />
        </div>
    </div>
</div>

<div id="showTable" class="d-none">
    <table class='table table-bordered table-striped text-center'>
        <thead>
            <tr class='text-bold bg-info'>
                <th>Selected All <br><input type='checkbox' id='selectAll' /></th>
                <th>Store Name</th>
                <th>Order No.</th>
                <th>Awb No.</th>
                <th>Courier Name</th>
                <th>Order Date</th>
                <th>SKU</th>
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
                url: "{{route('lable.search.amazon-order-id')}}",
                data: {
                    'order_id': form_data,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(response) {
                    if (response.success) {
                        $('#showTable').removeClass('d-none');
                        $('#checkTable').html(response.success);
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
                url: "{{route('lable.update.tracking-details')}}",
                data: {
                    'order_id': order_id,
                    'tracking_id': tracking_id,
                    'courier': courier,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(response) {

                }
            });
        });
    });
</script>
@stop