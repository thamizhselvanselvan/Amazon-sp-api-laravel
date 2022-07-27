@extends('adminlte::page')

@section('title', ' Amazon Business API ')

@section('css')

<link rel="stylesheet" href="/css/styles.css">
@stop
@section('content_header')
<h1 class="m-0 text-dark">Get Products By Asins</h1>
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
        <x-adminlte-input label="Enter ASIN:" name="asin" id="product_asin4" type="text" placeholder="asin...." />
    </div>

    <div class="col ">
        <div style="margin-top: 2.3rem;">

            <!-- //<a href="/shipment/storeshipment"> -->
            <x-adminlte-button label="Submit" theme="primary" icon="fas fa-file-upload" id="create" class="btn-sm product_search" />
            <!-- </a> -->


        </div>
    </div>
</div>
<pre>
  <div class="col" id="datapro4">
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
    $("#datapro3").hide();

    $(".product_search").on('click', function(e) {
        $("#datapro3").show();
    });
    $(".product_search").on("click", function() {
        let asin = $('#product_asin4').val();
        let length = asin.length;
        if (asin.length < 10 || asin.length > 10) {
            alert("Invalid ASIN");
            return false;
        }

        $.ajax({
            method: 'GET',
            url: '/business/asin/details',
            data: {
                'asin': asin,
                "_token": "{{ csrf_token() }}",
            },
            response: 'json',
            success: function(response) {



                $var = prettifyJson(response, true);
                let html = '';

                html += "<h5> ASIN Details :" + $var + "</h5>";




                $("#datapro4").html(html);
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