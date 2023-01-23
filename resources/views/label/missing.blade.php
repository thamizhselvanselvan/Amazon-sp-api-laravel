@extends('adminlte::page')
@section('title', 'Label')

@section('css')

<link rel="stylesheet" href="/css/styles.css">
<style>
    .table td {
        padding: 0;
        padding-left: 5px;

    }

    .btn-group-sm .btn,
    .btn-sm {
        padding-left: 0.2rem 0.2rem;
    }
</style>
@stop

@section('content_header')
<div class="row">
    <h1 class="m-0 text-dark col">Find Missing Orders</h1>
</div>
@stop

@section('content')
<form class="container-fluid" action="{{ route('label.missing.order.id') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <div class="col-2">
            <x-adminlte-select name="seller_id" label="Select Store">
                @foreach ($selected_store as $store)
                <option value="{{ $store->seller_id }},{{$store->store_name}},{{ $store->country_code }},{{$store->source}}"> {{ $store->store_name }}
                    [{{ $store->country_code }}]</option>
                @endforeach
            </x-adminlte-select>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <label>Amazon Order Id</label>
            <textarea class="form-control" rows="3" placeholder="Eg:- Amazon Order Id: 333-7777777-7777777" name="order_id"></textarea>
            <div class="text-right m-2">
                <a href='asin_save_in'>
                    <x-adminlte-button label='Submit' class="btn-sm" theme="primary" icon="fas fa-file-upload" type="submit" />
                </a>
            </div>
        </div>
    </div>
</form>
<!-- </div> -->

@stop

@section('js')

</script>
@stop