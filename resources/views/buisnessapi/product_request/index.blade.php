@extends('adminlte::page')

@section('title', ' Amazon Business API ')

@section('css')

<link rel="stylesheet" href="/css/styles.css">
@stop
@section('content_header')
<h1 class="m-0 text-dark">Products Request</h1>
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
        <x-adminlte-input label="Enter ASIN:" name="asin" id="product_asin" type="text" placeholder="asin...." />
    </div>

    <div class="col ">
        <div style="margin-top: 2.3rem;">

            <!-- //<a href="/shipment/storeshipment"> -->
            <x-adminlte-button label="Submit" theme="primary" icon="fas fa-file-upload" id="create" class="btn-sm product_search" />
            <!-- </a> -->


        </div>
    </div>
</div>
<div class="col" id="datapro2">
    <h4> </h4>
    <h4> </h4>
    <h4> </h4>
    <h4> </h4>
    <h4> </h4>
    <h4> </h4>
    <h4> </h4>
    <h4> </h4>
    <h4> </h4>
    <h4> </h4>

</div>


@stop
@section('js')

<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $("#datapro").hide();

    $(".product_search").on('click', function(e) {
        $("#datapro").show();
    });
    $(".product_search").on("click", function() {
        let asin = $('#asin').val();


        $.ajax({
            method: 'GET',
            url: '/buisness/product/details',
            data: {
                'asin': asin,
                "_token": "{{ csrf_token() }}",
            },
            response: 'json',
            success: function(response) {
                let html = '';


                let weight = 'Item Weight';
                let model = 'Item model number';
                let Dimensions = 'Product Dimensions';
                let part = 'Part Number';

                html += "<h4> ASIN : " + response[0].asin + "</h4>";
                html += "<h4> ASIN Type : " + response[0].asinType + "</h4>";
                html += "<h4> Item Name : " + response[0].title + " </h4>";
                html += "<h4> Features : " + response[0].features[0] + " </h4>";
                html += "<h4> Manufacturer :" + response[0].productOverview.Manufacturer + " </h4>";
                html += "<h4> Part Number :" + response[0].productOverview[part] + " </h4>";
                html += "<h4> Weight :" + response[0].productOverview[weight] + " </h4>";
                html += "<h4> Model :" + response[0].productOverview[model] + " </h4>";
                html += "<h4> Dimentions :" + response[0].productOverview[Dimensions] + " </h4>";
                html += "<h4> URL:  " + response[0].url + " </h4>";

                $("#datapro2").html(html);
            },

            error: function(response) {
                console.log(response);
                alert('Error');
            }
        });
    });
</script>
@stop