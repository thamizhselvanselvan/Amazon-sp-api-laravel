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
<div class="row">
    <div class="col-2">
        <input type="radio" name="size" id="asinwise">
        <label for=" entire">Search by ASIN</label>
    </div>
    <div class="col-9">
        <input type="radio" name="size" id="keywise">
        <label for="ware">Search by Keyword </label>
    </div>
</div>
<div class="row">
    <div class="col-2" id="asin_search">
        <x-adminlte-input label=" Enter ASIN:" name="asin" id="asin" type="text" placeholder=" Asin...." />
    </div>

    <div class="col-2" id="key_search">
        <x-adminlte-input label="Enter Keyword:" name="Keyword" id="Keyword" type="text" placeholder="Keyword...." />
    </div>
</div>
<div class="row">
    <div class="col ">
        <!-- //<a href="/shipment/storeshipment"> -->
        <x-adminlte-button label="Submit" theme="primary" icon="fas fa-file-upload" id="create" class="btn-sm product_search" />
        <!-- </a> -->
    </div>
</div>
<pre>
<div class="col" id="datapro">
</div>
</pre>


@stop
@section('js')

<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $("#asin_search").hide();
    $("#key_search").hide();
    $("#create").hide();

    $("#asinwise").on('click', function(e) {
        $("#asin_search").show();
        $('#Keyword').val('');
        $("#key_search").hide();
    });
    $("#keywise").on('click', function(e) {
        $("#key_search").show();
        $('#asin').val('');
        $("#asin_search").hide();
    });
    $("#keywise,#asinwise").on('click', function(e) {
        $("#create").show();
    });

    $(".product_search").on("click", function() {




        let asin = $('#asin').val();
        let key_word = $('#Keyword').val();
        let type = '';
        let length = asin.length;


        key = key_word.replace(' ', '_');

        if (type == key) {

            if (asin.length < 10 || asin.length > 10) {

                alert("Invalid ASIN");
                return false;
            }
            data = {
                'asin': asin,

            }
        } else {

            data = {
                'key': key,
            }
        }
  
        $.ajax({
            method: 'GET',
            url: '/product/details/search',
            data: {
                'data': data,
                "_token": "{{ csrf_token() }}",
            },
            response: 'json',
            success: function(response) {
                $var = prettifyJson(response, true);

                let html = '';
                html += $var;

                $("#datapro").html(html);
            },

            error: function(response) {
                console.log(response);
                alert('Error');
            }
        });
    });

    function prettifyJson(json, prettify) {
        if (typeof json !== 'string') {
            if (prettify) {
                json = JSON.stringify(json, undefined, 4);
            } else {
                json = JSON.stringify(json);
            }
        }
        return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g,
            function(match) {
                let cls = "<span>";
                if (/^"/.test(match)) {
                    if (/:$/.test(match)) {
                        cls = "<span class='text-danger'>";
                    } else {
                        cls = "<span>";
                    }
                } else if (/true|false/.test(match)) {
                    cls = "<span class='text-primary'>";
                } else if (/null/.test(match)) {
                    cls = "<span class='text-info'>";
                }
                return cls + match + "</span>";
            }
        );
    }
</script>
@stop