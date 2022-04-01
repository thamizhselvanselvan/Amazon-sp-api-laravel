@extends('adminlte::page')
@section('title', 'Orders List')

@section('content_header')
<h1 class="m-0 text-dark">Order List</h1>
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

        <h2 class="mb-4">
        <a href="{{route('getOrder.list')}}">
                <x-adminlte-button label="Orders List" theme="primary" icon="fas fa-file-import" />
            </a>
        </h2>
    </div>
</div>

@stop

@section('js')
<script type="text/javascript">
    $(function() {
        //     $(document).on('change', '#download_amazon_product', function(){
        //         // alert($('#download_amazon_product').val());
        //         $.ajax({
        //             method: 'get',
        //             url: 'other-product/download/0',
        //             data:{
        //                 "_token": "{{ csrf_token() }}",
        //                 "_method": 'get',
        //             },
        //             success: function() {

        //                 // yajra_table.ajax.reload();

        //             }
        //         })      
        // // alert('change');
        // });
    });
</script>

@stop