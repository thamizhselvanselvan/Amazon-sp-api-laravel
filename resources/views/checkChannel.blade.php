@extends('adminlte::page')

@section('title', 'Amazon.com product')

@section('content_header')
<h1 class="m-0 text-dark">checking Broadcast Channel</h1>
@stop

@section('css')

@stop

@section('js')
<script src="{{ asset('js/app.js') }}"></script>
<script>
// let p_channel = window.Echo.private("testing-channel");

// p_channel.listen('checkEvent', function(p_data) {
//     // console.log('success');
//     console.log(p_data.catalog);
// })
</script>
@stop
