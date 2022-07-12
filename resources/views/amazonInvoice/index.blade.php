@extends('adminlte::page')

@section('title', 'Amazon.in product')

@section('content_header')
<div class="row">
    <h1 class="m-0 text-dark">Amazon Invoice Management</h1>
    <div class="col text-right">
        <a href='{{route("amazon.invoice.upload")}}'>
            <x-adminlte-button label='Upload Amazon Invoice' class="" theme="primary" icon="fas fa-file-import" />
        </a>
    </div>
</div>
@stop

@section('css')

@endsection

@section('content')


@stop

@section('js')
<script type="text/javascript">

</script>
@stop