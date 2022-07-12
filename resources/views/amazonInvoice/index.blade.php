@extends('adminlte::page')

@section('title', 'Amazon.in product')

@section('content_header')
<h1 class="m-0 text-dark">Amazon Invoice Management</h1>
<div class="col text-right">
    <x-adminlte-button label='Export Catalog' class="product_export_modal_open" theme="primary" icon="fas fa-file-export" />
<a href='asin_upload_in'>
    <x-adminlte-button label='Export By ASIN' class="" theme="primary" icon="fas fa-file-export" />
</a>
    <x-adminlte-button label='Download' class="file_download_modal_btn" theme="success" icon="fas fa-download" />
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