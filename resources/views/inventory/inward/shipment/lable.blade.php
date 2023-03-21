@extends('adminlte::page')

@section('title', 'Print label')

@section('css')

<link rel="stylesheet" href="/css/styles.css">
<style>
    h5 {
        font-weight: bold;
        font-size: 0.9em;
    }

    h6 {
        overflow: hidden;
        max-width: 40ch;
        font-size: 0.875em;
        height: 1.2em;
        /* exactly 2 lines */

    }

    footer {
        display: none;
    }

    .bar_inv {
        height: 55px;
        width: 300px;
    }

    .print-label {
        border: 1px solid lightgrey;
        width: 300px;
        margin: 2px;
    }

    .breaker {
        display: block;
        border: 1px solid lightgrey;
        width: 100%;
    }

    .print-page-breaker {
        width: 100%;
    }

    @media print {
        .print-page-breaker {
            page-break-after: always;
            border: 1px solid red;
        }
    }
</style>



<style>
    .grid {
        display: inline-grid;
        grid-template-rows: repeat(12, 1fr);
        grid-template-columns: auto;
        /* column-gap: 1px;
                row-gap: 5px; */
        justify-items: stretch;

    }

    .item {

        border: 1px solid lightgrey;
        margin: 2px;
        padding: 10PX 10px;
        width: 327px;
        margin-bottom: 70px
    }
</style>



@stop
@section('content_header')
<div class="row">
    <div style="margin-top:0.0rem;">
        <a href="{{ route('shipments.index') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-long-arrow-alt-left"></i> Back
        </a>
    </div>
    <div class="col-2">
        <button type="button" class="btn btn-primary btn-sm" id="Export_to_pdf"><i class="fas fa-download"></i> Download
            PDF</button>

    </div>
    <div class="col-3">
        <h3>Shipment ID : {{ $viewlable->ship_id }} </h3><br>
        <input type="hidden" name="ship" id="ship" value="{{ $viewlable->ship_id }}">
    </div>
</div>
@stop
@section('content')


<div class="row">
    <div class="col-md-4">
        @php
        $column = 1;
        $counter = 1;
        @endphp
        @foreach ($lable as $key => $val)
        @for ($i = 1; $i <= $quant[$key]; $i++) <div class="item pt-2">
            <span>{{ $val['asin'] }} </span>
            <span><img class="bar_inv" src="data:image/png;base64,{!! $bar_code[$key] !!}" /></span>
            <span style="font-size: 14px;">{{substr_replace($val['item_name'],'',37)}}</span>
            <span style="font-size: 14px;">id = {{($val['id'])}}</span>

    </div>

    @if ($column == 8 && $counter != 24)
    @php
    $column = 0;
    @endphp
</div>
<div class="col-md-4">
    @endif

    @if ($counter == 24)
    @php
    $column = 0;
    @endphp
</div>
</div>
<div class="print-page-breaker" style="clear: both;"> </div>
<div class="row">
    <div class="col-md-4">

        @php $counter = 0; @endphp
        @endif
        @php $counter++; @endphp
        @php
        $column++;
        @endphp
        @endfor
        @endforeach
    </div>
</div>

@stop



@section('js')
<script>
    $(document).ready(function() {
        $('#Export_to_pdf').click(function(e) {
            $('#Export_to_pdf').attr("disabled", true);
            $('#Export_to_pdf').html("<span class='spinner-border spinner-border-sm' role='status' aria-hidden='true'></span> Please wait");


            e.preventDefault();
            var url = $(location).attr('href');
            var ship_id = $.trim($('#ship').val());

            $.ajax({
                method: 'POST',
                url: "{{ url('shipment/lable/export-pdf') }}",
                data: {
                    'id': ship_id,
                    'url': url,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(response) {
                    $('#Export_to_pdf').attr("disabled", false);
                    $('#Export_to_pdf').html("<i class='fas fa-download'></i> DownloadPDF");

                    window.location.href = '/inventory/Shipment/download/' + ship_id;
                    alert(' pdf Downloaded  successfully');
                }
            });
        });
    });
</script>
@stop