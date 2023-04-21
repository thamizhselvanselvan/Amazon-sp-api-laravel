@extends('adminlte::page')
@section('title', 'Price Push')

@section('content_header')
<div class="container-fluid">
    <h1 class="m-0 text-dark col">Price Push To Amazon</h1>
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
<div class="container-fluid">
    <div class="row">
        <div class="col-2">
            <x-adminlte-select name="ware_id" id="store_select" label="Select Store">
                <option value="0">Select Store</option>
                @foreach($stores as $store)
                <option value="{{$store->seller_id}}-{{$store->country_code}}">{{$store->store_name}}</option>
                @endforeach
            </x-adminlte-select>
        </div>
        <div class="col-2">
            <x-adminlte-input label="Enter ASIN :" name="asin" id="asin" type="text" placeholder="asin..." />
        </div>
        <div class="col-2">
            <x-adminlte-input label="Enter SKU :" name="sku" id="sku" type="text" placeholder="sku..." />
        </div>
        <div class="col-2">
            <x-adminlte-input label="Enter Push price :" name="price" id="price" type="text" placeholder="price..." />
        </div>
        <div>
            <div style="margin-top: 2.0rem;">
                <x-adminlte-button label=" Submit" theme="success" icon="fas fa-save" type="submit" id="submit" />
            </div>
        </div>
    </div>
    <div class="row temp d-none">
        <div class="col-6">
            <h4><b>Click On Feedback Id To Know The Feedback Responce</b> :</h4>
            <h5 id="feed_id"></h5>

        </div>
    </div>
</div>

@stop

@section('js')
<script type="text/javascript">
    $('#submit').on('click', function() {

        let asin = $('#asin').val();
        let store_select = $('#store_select').val();
        let price = $('#price').val();
        let sku = $('#sku').val();

        if (asin == '') {
            alert('ASIN Required..!');
            return false;
        } else if (store_select == 0) {
            alert('store_select Required..!');
            return false;
        } else if (price == '') {
            alert('price Required..!');
            return false;
        }
        let data = {
            "asin": asin,
            "store_select": store_select,
            "price": price,
            "product_sku": sku,
        };
        $.ajax({
            url: "{{route('sanjay.test')}}",
            method: "get",
            data: {
                "data": data,
                "_token": "{{ csrf_token() }}",
            },
            success: function(response) {
                let store_select = $('#store_select').val();
                console.log(response)
                $(".temp").removeClass("d-none")
                if (store_select == '') {
                    alert('No store Selected please elect The Store and Try again');
                    return false;
                }
                let files = '';
                feed_id = response;
                files += "<li class='p-0 m-0'>";
                files += " Feedback Id = <a href='/price/feed/check/" + feed_id + '-' + store_select + "'target='_blank'>" +
                    response + '&nbsp; ' + "</a>";
                files += "</li>";

                $("#feed_id").append(files)

            },
            error: function(result) {
                console.log(response)

            }
        });
    });
</script>
@stop