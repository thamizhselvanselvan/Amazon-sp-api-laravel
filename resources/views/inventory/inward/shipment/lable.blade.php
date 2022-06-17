@extends('adminlte::page')

@section('title', 'Print lable')

@section('css')

<link rel="stylesheet" href="/css/styles.css">
<style>
   h5{
    font-weight: bold;
   }
</style>
@stop
@section('content_header')
<button type="button"  class="btn btn-primary btn-sm" id="Export_to_pdf"><i class="fas fa-print"></i>Print as PDF</button>
@stop
@section('content')
<h3>Shipment ID : {{ $view->ship_id }} </h3><br>
<input type="hidden" name="ship" id="ship" value="{{ $view->ship_id }}">


@php
$data = json_decode($view['items'], true);
$data = (count($data) > 0) ? $data : [];
@endphp
@foreach ($data as $key => $val)
<h5>New</h5>
<h4>{!! $bar_code !!}</h4>
<h6>{{$val['asin']}}</h6>
<h6>{{$val['item_name']}}</h6>
@endforeach
@stop


@section('js')
<script>
    $(document).ready(function() {
        $('#Export_to_pdf').click(function(e) {
            e.preventDefault();
            var url = $(location).attr('href');
            var ship_id= $.trim($('#ship').val());

            $.ajax({
                method: 'POST',
                url: "{{ url('shipment/lable/export-pdf')}}",
                data: {
                    'id' :ship_id,
                    'url': url,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(response) {

                    window.location.href = '/Shipment/download/'+ship_id;
                    alert(' pdf Downloaded  successfully');
                }
            });
        });
    });
</script>
@stop