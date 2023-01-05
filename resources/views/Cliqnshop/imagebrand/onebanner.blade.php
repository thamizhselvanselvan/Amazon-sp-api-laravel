@extends('adminlte::page')

@section('title', 'Banner Section')

@section('css')
<link rel="stylesheet" href="/css/styles.css">
@stop

@section('content_header')
<div class="row">
    <div class="col=1"></div>
</div>
<div class="row justify-content-center">
    <h3>Cliqnshop 1 Banner Section </h3>
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
<form method="post" action="{{route('cliqnshop.one.banner.store')}}" enctype="multipart/form-data">
    @csrf
    <div class="row justify-content-center">
        <div class="col-3">
            <div class="form-group">
                <label for="text">Redirection URL</label>
                <x-adminlte-input class="form-control " type="text" placeholder="Enter Redirect URL For Image" name="url" id="url" />
            </div>
        </div>

        <div class="col-3">
            <div class="form-group">
                <x-adminlte-select name="country" label="Select Country" name="country" id="source">
                    <option value=''>Select Country</option>
                    @foreach ($countrys as $country)
                    <option value="{{ $country->siteid }}">{{$country->code }}</option>
                    @endforeach
                </x-adminlte-select>
            </div>

        </div>
    </div>
    <div class="row justify-content-center">

        <div class="col-3">
            <div class="form-group">
                <label for="text">Primary Text</label>
                <x-adminlte-input class="form-control " type="text" placeholder="Enter primary Text" name="primary_text" id="" />
            </div>
        </div>
        <div class="col-3">
            <div class="form-group">
                <label for="text">secondary Text</label>
                <x-adminlte-input class="form-control " type="text" placeholder="Enter secondary Text" name="secondary_text" id="" />
            </div>
        </div>
    </div>
    <div class="row justify-content-center">

        <div class="col-6">
            <div class="form-group">
                <x-adminlte-input type="file" name="selected_image" label="Select Image"></x-adminlte-input>
            </div>

        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-2">
            <x-adminlte-button label="Submit" theme="primary" id="img_submit" icon="fas fa-upload" type="submit" />
        </div>
    </div>
</form>
<div class="row justify-content-center d-none" id="row">
    <div class="col">
        <h2>Current Image</h2>
        
        <img src="" alt="" id="cur_image" width="400" height="300">

        <!-- <input type="image" src="" alt="" id="cur_image" width="48" height="48"> -->
    </div>
</div>
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
            url: "{{route('cliqnshop.onebanner')}}",
            data: {
                'country': cid,
                "_token": "{{ csrf_token() }}",
            },
            success: function(response) {

                console.log(response.data);
                $('#row').removeClass('d-none');
                $("#cur_image").attr("src", response.data);

                // $("#image_fill").empty();
                // let asins = (response['data']);

                // $("#image_fill").append(asins);
                // console.log(asins);
            },
            error: function(response) {
                console.log(response);
            }
        });
    });
</script>
@stop