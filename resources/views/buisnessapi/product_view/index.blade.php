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
<table class="table table-bordered yajra-datatable table-striped">
    <thead>
        <tr>
          <th>ID</th>
          <th>asin</th>
          <th>asin_type</th>
          <th>signedProductId </th>
          <th>offers</th>
          <th>availability</th>
          <th>buyingGuidance</th>
          <th>fulfillmentType</th>
          <th>merchant</th>
          <th>offerId</th>
          <th>price</th>
          <th>listPrice</th>
          <th>productCondition</th>
          <th>condition</th>
          <th>quantityLimits</th>
          <th>deliveryInformation</th>
          <th>features</th>
          <th>taxonomies</th>
          <th>title</th>
          <th>url</th>
          <th>productOverview</th>
          <th>productVariations</th>

        </tr>
    </thead>

    <tbody>
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

    $(".product_search").on("click", function() {
        let asin = $('#product_asin3').val();

        let length = asin.length;
        if (asin.length < 10 || asin.length > 10) {

            alert("Invalid ASIN");
            return false;
        }
        $.ajax({
            method: 'GET',
            url: '/buisness/product/offers',
            data: {
                'asin': asin,
                "_token": "{{ csrf_token() }}",
            },
            response: 'json',
            success: function(response) {},

            error: function(response) {
                console.log(response);
                alert('Error');
            }
        });

    });
</script>
@stop