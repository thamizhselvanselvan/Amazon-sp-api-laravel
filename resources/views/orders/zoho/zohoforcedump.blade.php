@extends('adminlte::page')

@section('title', 'Zoho Dump')

@section('css')

<link rel="stylesheet" href="/css/styles.css">

@stop

@section('content_header')
<div class="row">
    <div class="col-12">
        <h1 class="m-0 text-dark"> Zoho Dump And Sync
            <x-adminlte-button label="Zoho Sync" theme="success" icon="fas fa-redo-alt" id="refresh" class="text-right" data-toggle="modal" data-target="#zohosyncmodal" />
        </h1>
    </div>
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
            @if($message = Session::get('error'))
            <div class="alert alert-danger alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $message }}</strong>
            </div>
            @endif
            @if($message = Session::get('warning'))
            <div class="alert alert-warning alert-block alert">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $message }}</strong>
            </div>
            @endif
        </div>
    </div>
</div>


<div class="modal fade" id="zohosyncmodal" tabindex="-1" role="dialog" aria-labelledby="zohosyncmodallabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="new_asin">Zoho Sync</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div id="warning" class="alert alert-warning alert-dismissible fade show" role="alert">
                please Upload <strong>10 Order Id </strong>At a Time.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="row" id="multi-file-upload" method="POST" action="{{route('orders.zoho.sync')}}" accept-charset="utf-8" enctype="multipart/form-data">
                    @csrf
                    <div class="col-12">
                        <x-adminlte-select name="store_data" label="Select Store:" id="store_select">
                            <option value="0">Select Store</option>
                            @foreach($stores as $store)
                            <option value="{{$store->seller_id .'_'.$store->country_code}}">{{$store->store_name}}</option>
                            @endforeach
                        </x-adminlte-select>

                    </div>
                    <div class="col-4" id="order_id">
                        <div class="form-group">
                            <label>Enter order ID's:</label>
                            <div class="autocomplete" style="width:470px;">
                                <textarea name="order_ids" rows="5" placeholder="Add order id's here..." id="" type=" text" autocomplete="off" class="form-control up_asin"></textarea>
                            </div>
                        </div>
                    </div>
            </div>
            <div class="modal-footer">
                <div class="col text-left">
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fas fa-window-close" aria-hidden="true"></i> Close</button>
                    <x-adminlte-button label="Submit" theme="primary" icon="fas fa-file-upload" id="upload" class="btn-sm upload_asin_btn" type="submit" />
                </div>
            </div>
            </form>
        </div>
    </div>
</div>





<form action="{{Route('orders.zoho.force.dump')}}" method="POST" id="admin_user">
    @csrf
    <div class="row">
        <div class="col-3">

            <x-adminlte-select name="country_code" label="Select Store:" id="store_select">
                <option value="0">Select Store</option>
                @foreach($stores as $store)
                <option value="{{$store->seller_id .'_'.$store->country_code}}">{{$store->store_name}}</option>
                @endforeach
            </x-adminlte-select>

        </div>
    </div>
    <div class="row">

        <div class="col-2" id="order_id">
            <div class="form-group">
                <label>Enter order ID's:</label>
                <div class="autocomplete" style="width:375px;">
                    <textarea name="order_ids" rows="5" placeholder="Add order id's here..." id="" type=" text" autocomplete="off" class="form-control up_asin"></textarea>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-1">
            <x-adminlte-button label="Submit" theme="primary" icon="fas fa-file-upload" id="upload" class="btn-sm upload_asin_btn" type="submit" />
        </div>
    </div>
</form>


@stop


@section('js')
<script type="text/javascript">
    $("#upload").on('click', function(e) {


        let data = $('.up_asin').val();
        let store = $('#store_select').val();
        if (store == '0') {
            alert('Please Select Store');
            return false;
        } else if (data == '') {
            alert('please Enter Order ID');
            return false;
        }

    });

    // $(document).ready(function() {

    //     $.ajax({
    //             url: "{{route('orders.zoho.force.dump.view')}}",
    //             method: 'get',
    //             data: {
    //                 'command': 'command',
    //                 "_token": "{{ csrf_token() }}",
    //             },
    //             success: function(result) {

    //             if (result.data.hasOwnProperty("error")) {
    //                 if ((result['data']['error']['0']['status']) == '0') {
    //                     alert('Previous Order ID is Still Processing Please Wait...');
    //                     document.getElementById("upload").disabled = true;
    //                 }
    //             }
    //         },
    //         error: function() {
    //             alert('ERROR');
    //         }
    //     });

    // });
</script>
@stop