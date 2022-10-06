@extends('adminlte::page')

@section('title', 'Cliqnshop Orders')

@section('content_header')

<div class="row">
    <h3>Cliqnshop Order Details</h3>

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
<div class="row">
    <h2 class="ml-2">
        <x-adminlte-button label="View Orders Placed" theme="primary" class="btn-sm" icon="fas fa-eye" id="vieworders" />
    </h2>
    <h2 class="ml-2">
        <x-adminlte-button label="View Orders Pending" theme="primary" class="btn-sm" icon="fas fa-eye" id="pendingorders" />
    </h2>
    <h5 class="mb-4 text-right col">
        <div class="search">
            <label>
                Search:
                <input type="text" id="myInput" class="d-inline-block" placeholder="search asin" autocomplete="off" />
            </label>
        </div>
    </h5>
</div>

<div class="modal " id="selectoffer">
    <div class="modal-dialog modal-lg">
        <div class="modal-content modal-lg">
            <div class="modal-header">
                <h4 class="modal-title text-center">Select Offer </h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body offerselect">

            </div>
            <div class="modal-footer">
                <div class="col-2 float-righr mt-2">
                    <x-adminlte-button label="Place Order" theme="success" class="btn btn-sm " id="place_order" icon="fas fa-file-export " />
                </div>
                <div class="col-1 float-right mt-2">
                    <button type="button" class="btn btn-sm btn-danger" id="close" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<table class="table table-bordered yajra-datatable table-striped" id='orderstable'>
    <thead>
        <tr class='text-bold bg-info'>
            <!-- <th>ID</th> -->
            <th>ASIN</th>
            <th>Item Name</th>
            <th>Price</th>
            <th>Quantity</th>
            <th> Status</th>
        </tr>
    </thead>
    <tbody id="data_display">

    </tbody>
</table>
<table class="table table-bordered yajra-datatable table-striped" id='orderspending'>
    <thead>
        <tr class='text-bold bg-info'>
            <th>ASIN</th>
            <th>Item Name</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody id="data_display_pending">

    </tbody>
</table>
@stop
@section('js')
<script type="text/javascript">
    $("#orderstable").hide();
    $(".search").hide();
    $("#orderspending").hide();
    $(document).ready(function() {
        $("#myInput").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#data_display tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });
    $('#vieworders').on('click', function() {
        $('#data_display').empty();
        $("#orderstable").show();
        $("#orderspending").hide();
        $(".search").show();
        $.ajax({
            method: 'GET',
            url: '/business/orders/view/',
            data: {
                'cliq': 'cliq',
                "_token": "{{ csrf_token() }}",
            },
            'dataType': 'json',
            success: function(response) {
                let html = '';
                $status = '';
                $.each(response.data, function(index, value) {
                    html += "<tr>";
                    html += "<tr class='table_row'>";
                    html += "<td name='asin[]'>" + value.prodcode + "</td>";
                    html += "<td name='name[]'>" + value.name + "</td>";
                    html += "<td name='name[]'>" + value.price + "</td>";
                    html += "<td name='name[]'>" + value.quantity + "</td>";
                    html += "<td name='name[]'>" + 'Booked' + "</td>";
                    html += "</tr>";

                });
                $("#orderstable").append(html);
            },
            error: function(response) {
                console.log(responce)
            }
        });

    });

    $('#close').click(function() {
        $('#selectoffer').modal('hide');
    });

    $('#pendingorders').on('click', function() {

        $("#data_display_pending").empty();
        $("#orderspending").show();
        $("#orderstable").hide();
        $(".search").show();
        $.ajax({
            method: 'GET',
            url: '/business/orders/pending/',
            data: {
                'cliq': 'cliq',
                "_token": "{{ csrf_token() }}",
            },
            'dataType': 'json',
            success: function(response) {
                if (response.data == '') {
                    alert("No Pending Orders...")
                }
                let html = '';
                $status = '';
                $.each(response.data, function(index, value) {

                    html += "<tr>";
                    html += "<tr class='table_row'>";
                    html += "<td name='asin'>" + value.prodcode + "</td>";
                    html += "<td name='name'>" + value.name + "</td>";
                    html += "<td name='price'>" + value.price + "</td>";
                    html += "<td name='q'>" + value.quantity + "</td>";
                    html += '<td> <button type="button" id="offers" class="btn btn-info offers1">Select Offers And Place Order</button></td>'
                    html += "</tr>";

                });
                $("#data_display_pending").append(html);

            },
            error: function(response) {
                alert('Something Went Wrong..')
            }

        });

    });

    $(document).on("click", ".offers1", function() {
        let self = $(this);
        let asin = self.parent().prev().prev().prev().prev().text();
        $.ajax({
            method: 'GET',
            url: '/business/offers_view/',
            data: {
                'asin': asin,
                "_token": "{{ csrf_token() }}",
            },
            // 'dataType': 'json',
            success: function(response) {

                $('#selectoffer').modal('show');
                $('.offerselect').html(response);
            },
            error: function(response) {
                alert('Something Went Wrong.. or No offer found')
            }
        });

    });


    $('#place_order').on("click", function() {
        $('.display-data').addClass('d-block');
        let catalog_asins = $('.Asins').val();
        let source = $('input[name="oid"]:checked').val();

        if (!$('input[name="oid"]:checked').val()) {
            alert('Please choose an Offer');
            return false;
        } else {
            let offerid = $("input[name='oid']").val();
            let asin = $("input[name='asin']").val();
            let name = $("input[name='item_name']").val();

            $.ajax({
                method: 'post',
                url: "/business/order/book/",
                data: {
                    "offerid": offerid,
                    "asin": asin,
                    "item_name": name,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(response) {
                    console.log(response);
                    alert('Booked Successfully')
                },
                error: function(response) {
                    console.log(response);
                    alert('not Booked')
                }
            });

        }


    });
</script>
@stop