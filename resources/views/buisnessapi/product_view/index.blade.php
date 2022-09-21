@extends('adminlte::page')

@section('title', ' Amazon Business API ')

@section('css')

<link rel="stylesheet" href="/css/styles.css">
@stop
@section('content_header')
<h1 class="m-0 text-dark">Search Product</h1>
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
<div class="row">


    <div class="col-2">
        <x-adminlte-input label="Enter ASIN:" name="asin" id="product_asin3" type="text" placeholder="asin...." />
    </div>

    <div class="col ">
        <div style="margin-top: 2.3rem;">

            <!-- //<a href="/shipment/storeshipment"> -->
            <x-adminlte-button label="Submit" theme="primary" icon="fas fa-file-upload" id="create" class="btn-sm product_search" />
            <!-- </a> -->

        </div>
    </div>
</div>
<table class="table table-bordered yajra-datatable table-striped " id="pro_table">
    <thead>
        <tr class='text-bold bg-info'>
            <!-- <th>ID</th> -->
            <th>Asin</th>
            <th>Asin_type</th>
            <!-- <th>Signed ProductId </th> -->
            <!-- <th>Offers</th> -->
            <th>Availability</th>
            <th>Buying Guidance</th>
            <th>Fulfillment Type</th>
            <th>Merchant</th>
            <!-- <th>OfferId</th> -->
            <th>Price</th>
            <th>List Price</th>
            <th>Product Condition</th>
            <!-- <th>Condition</th> -->
            <th>Quantity Limits</th>
            <th>Delivery Information</th>
            <!-- <th>Features</th> -->
            <th>Taxonomies</th>
            <th>Title</th>
            <th>URL</th>
            <th>Product Overview</th>
            <!-- <th>Product Variations</th> -->
        </tr>
    </thead>
    <tbody id="product_table">
    </tbody>
</table>

@stop
@section('js')
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $("#pro_table").hide();

    $(".product_search").on('click', function(e) {
        $("#pro_table").show();
    });

    $(".product_search").on("click", function() {
        let asin = $('#product_asin3').val();

        let length = asin.length;
        if (asin.length < 10 || asin.length > 10) {

            alert("Invalid ASIN");
            return false;
        }
        $.ajax({
            method: 'GET',
            url: '/buisness/details',
            data: {
                'asin': asin,
                "_token": "{{ csrf_token() }}",
            },
            response: 'json',
            success: function(response) {
                console.log(response.data.length);

                if (response.data.length == 0) {

                    alert("Details Not Available");
                    return false;
                } {
                    let html = '';
                    html += "<tr>";
                    // html += "<td>" + id+ "</td>";
                    html += "<td>" + response.data[0].asin + "</td>";
                    html += "<td>" + response.data[0].asin_type + "</td>";
                    // html += "<td>" + response.data[0].signedProductId + "</td>";
                    // html += "<td>" + response.data[0].offers + "</td>";
                    html += "<td>" + response.data[0].availability + "</td>";
                    html += "<td>" + response.data[0].buyingGuidance + "</td>";
                    html += "<td>" + response.data[0].fulfillmentType + "</td>";
                    html += "<td>" + response.data[0].merchant + "</td>";
                    // html += "<td>" + response.data[0].offerId + "</td>";
                    html += "<td>" + response.data[0].price + "</td>";
                    html += "<td>" + response.data[0].listPrice + "</td>";
                    html += "<td>" + response.data[0].productCondition + "</td>";
                    // html += "<td>" + response.data[0].condition + "</td>";
                    html += "<td>" + response.data[0].quantityLimits + "</td>";
                    html += "<td>" + response.data[0].deliveryInformation + "</td>";
                    // html += "<td>" + response.data[0].features + "</td>";
                    html += "<td>" + response.data[0].taxonomies + "</td>";
                    html += "<td>" + response.data[0].title + "</td>";
                    html += "<td>" + response.data[0].url + "</td>";
                    html += "<td>" + response.data[0].productOverview + "</td>";
                    // html += "<td>" + response.data[0].productVariations + "</td>";
                    html += "</tr>";

                    $("#product_table").html(html);
                }
            },

            error: function(response) {
                console.log(response);
                alert('Error');
            }
        });

    });
</script>
@stop