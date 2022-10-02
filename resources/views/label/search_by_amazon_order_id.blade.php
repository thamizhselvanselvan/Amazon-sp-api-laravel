@extends('adminlte::page')
@section('title', 'Label')

@section('css')


<link rel="stylesheet" href="/css/styles.css">
@stop

@section('content_header')
<div class="row">
    <a href="{{route('label.manage')}}">
        <x-adminlte-button label='Back' class="btn-sm" theme="primary" icon="fas fa-long-arrow-alt-left" type="submit" />
    </a>
    <h1 class="m-0 text-dark col-3">Label Search By Order Id</h1>
</div>
@stop

@section('content')

@csrf
<div class="row">
    <div class="col">
        <label>Amazon Order Id</label>
        <textarea class="form-control" rows="3" placeholder="Eg:- Amazon Order Id: 333-7777777-7777777" name="order_id"></textarea>
        <div class="text-right m-2">
            <x-adminlte-button label='Search' class="btn-sm search-amazon-order-id" theme="primary" icon="fas fa-file-upload" type="submit" />
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    $(document).ready(function() {

        $('.search-amazon-order-id').on('click', function() {
            var form_data = $('.form-control').val();
            $.ajax({
                method: 'POST',
                url: "{{route('lable.search.amazon-order-id')}}",
                data: {
                    'order_id': form_data,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(response) {
                    //
                }
            });
        });
    });
</script>
@stop