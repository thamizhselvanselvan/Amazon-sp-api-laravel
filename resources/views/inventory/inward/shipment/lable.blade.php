@extends('adminlte::page')

@section('title', 'Print lable')

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

<h5>New</h5>

@foreach ($lable as $key => $val) 

   @php $counter = 1; @endphp 
 
  @for($i = 1; $i<= $quant[$key]; $i++)

    @if($counter==1) <div class="row">
     @endif
    <div class="col-4 sam">
        <h6>{{$val['asin']}} </h6>
        <h4>{!! $bar_code[$key] !!}</h4>
        <h6>{{$val['item_name']}}</h6>
    </div>
    @if($counter == 5)
        <div class="col-4 ">
            <p style="page-break-after: always;">&nbsp;</p>
        </div>
        </div> 
        @php
        $counter = 1;
        @endphp
    @endif
      @php $counter++; @endphp
   @endfor
@endforeach



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