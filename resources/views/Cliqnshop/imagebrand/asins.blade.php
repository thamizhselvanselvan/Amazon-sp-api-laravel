@extends('adminlte::page')

@section('title', 'Top Selling Section')

@section('css')
<link rel="stylesheet" href="/css/styles.css">
@stop

@section('content_header')
<div class="row">
    <div class="col=1"></div>
</div>
<div class="row ">
    <h3>Top Selling Section</h3>
</div>

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
        <div class="alert_display">
            @if ($message = Session::get('error'))
            <div class="alert alert-danger alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $message }}</strong>
            </div>
            @endif
        </div>
    </div>
</div>
<form method="post" action="{{route('cliqnshop.brand.store')}}" enctype="multipart/form-data">
    @csrf

    <div class="row">
        <div class="col-3">
            <div class="form-group">
                <x-adminlte-select name="image" label="Select Country" name="country" id="source">
                    <option value=''>Select Country</option>
                    @foreach ($countrys as $country)
                    <option value="{{ $country->siteid }}">{{$country->code }}</option>
                    @endforeach
                </x-adminlte-select>
            </div>

        </div>
    </div>

    <div class="row">
        <div class="col-2" id="asin">
            <div class="form-group">
                <label>Enter ASIN:</label>
                <div class="autocomplete" style="width:400px;">
                    <x-adminlte-textarea name="upload_asin" rows="5" placeholder="Add Asins here. MAX-100.." name="top_asin" type=" text" autocomplete="off" class="form-control" id="asin_fill" />
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-2">
            <x-adminlte-button label="Submit" theme="primary" id="img_submit" icon="fas fa-upload" type="submit" />
        </div>
    </div>
    </div>
</form>
@stop

@section('js')
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $('#source').change(function() {

        let cid = $('#source').val();
        if (cid == '') {
            return false;
        }

        $.ajax({
            method: 'get',
            url: "{{route('cliqnshop.brand')}}",
            data: {
                'country': cid,
                "_token": "{{ csrf_token() }}",
            },
            'dataType': 'json',
            success: function(response) {

                console.log(response);
                $("#asin_fill").empty();
                let asins = (response['data']);

                $("#asin_fill").append(asins);
                console.log(asins);
            },
            error: function(response) {

            }
        });
    });
</script>
@stop