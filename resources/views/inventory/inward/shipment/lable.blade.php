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
@stop
@section('content_header')
<div class="row">
    <div style="margin-top:0.0rem;">
        <a href="{{ route('shipments.index') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-long-arrow-alt-left"></i> Back
        </a>
    </div>
    <div class="col-2">
        <button type="button" class="btn btn-primary btn-sm" id="Export_to_pdf"><i class="fas fa-download"></i> Download PDF</button>

    </div>
    <div class="col-3">
        <h3>Shipment ID : {{ $viewlable->ship_id }} </h3><br>
        <input type="hidden" name="ship" id="ship" value="{{ $viewlable->ship_id }}">
    </div>
</div>
@stop
@section('content')

<div class="row">
    @php $counter = 1; @endphp
    @foreach ($lable as $key => $val)
    @for($i = 1; $i<= $quant[$key]; $i++) <div class="print-label col">
        <h6>{{$val['asin']}} </h6>
        <h4><img src="data:image/png;base64,{!! $bar_code[$key] !!}" /></h4>
        <h6>{{$val['item_name']}}</h6>
</div>
@php $counter++; @endphp
@if($counter == 42)
</div>
<div class="print-page-breaker"></div>
<div class="row">



    @php $counter = 1; @endphp
    @endif
    @endfor
    <div class="breaker"></div>
    @endforeach
</div>

@stop

@section('js')
<script>
    $(document).ready(function() {
        $('#Export_to_pdf').click(function(e) {
            e.preventDefault();
            var url = $(location).attr('href');
            var ship_id = $.trim($('#ship').val());

            $.ajax({
                method: 'POST',
                url: "{{ url('shipment/lable/export-pdf')}}",
                data: {
                    'id': ship_id,
                    'url': url,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(response) {

                    window.location.href = '/Shipment/download/' + ship_id;
                    alert(' pdf Downloaded  successfully');
                }
            });
        });
    });
</script>
@stop