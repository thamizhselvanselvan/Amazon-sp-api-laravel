@extends('adminlte::page')

@section('title', ' Amazon Business API')

@section('css')

<link rel="stylesheet" href="/css/styles.css">
@stop
@section('content_header')
<h1 class="m-0 text-dark">Search Products Request</h1>
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
<!-- <div class="row">
    <div class="col-2">
        <input type="radio" name="size" id="asinwise">
        <label for=" entire">Search By ASIN</label>
    </div>
    <div class="col-9">
        <input type="radio" name="size" id="keywise">
        <label for="ware">Search by Keyword </label>
    </div>
</div> -->
<div class="row">
    <div class="col-2" id="asin_search">
        <x-adminlte-input label=" Enter ASIN:" name="asin" id="asin" type="text" placeholder="Asin...." />
    </div>

    <!-- <div class="col-2" id="key_search">
        <x-adminlte-input label="Enter Keyword:" name="Keyword" id="Keyword" type="text" placeholder="Keyword...." />
    </div> -->
</div>
<div class="row">
    <div class="col ">
        <!-- //<a href="/shipment/storeshipment"> -->
        <x-adminlte-button label="Submit" theme="primary" icon="fas fa-file-upload" id="create" class="btn-sm product_search" />
        <!-- </a> -->
    </div>
</div>
<div class="col" id="datapro">
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

    // $("#datapro").hide();
    // $("#asin_search").hide();
    // $("#key_search").hide();
    // $("#create").hide();


    // $("#asinwise").on('click', function(e) {
    //     $("#asin_search").show();
    //     $("#key_search").hide();

    // });
    // $("#keywise").on('click', function(e) {
    //     $("#key_search").show();
    //     $("#asin_search").hide();
    // });
    // $("#keywise,#asinwise").on('click', function(e) {
    //     $("#create").show();

    // });
    $(".product_search").on("click", function() {
        let asin = $('#asin').val();
        let length = asin.length;
        if (asin.length <10 || asin.length >10)
        { 

            alert("Invalid ASIN");
            return false;
        }
            
        $.ajax({
            method: 'GET',
            url: '/product/details',
            data: {
                'asin': asin,
               
                "_token": "{{ csrf_token() }}",
            },
            response: 'json',
            success: function(response) {
                console.log(response);
                $var = (JSON.stringify(response));

                let html = '';

                html += "<h5> ASIN Details :" + $var + "</h5>";

                $("#datapro").html(html);
            },

            error: function(response) {
                console.log(response);
                alert('Invalid ASIN');
            }
        });
    });
</script>
@stop