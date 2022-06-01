@extends('adminlte::page')
@section('title', 'Invoice')

@section('content_header')
<div class="row">
    <h1 class="m-0 text-dark col">Invoice Management</h1>
    <h2 class="mb-4 text-right col">
        <a href="upload">
            <x-adminlte-button label="Upload Invoice Excel" theme="primary" icon="fas fa-file-upload" class="btn-sm" />
        </a>
        <a href="Export/view">
            <x-adminlte-button label="Download Invoice PDF" theme="primary" icon="fas fa-file-download" class="btn-sm" />
        </a>
        <a href="Download">
            <!-- <x-adminlte-button label="Download CSV file" theme="primary" icon="fas fa-file-download" /> -->
        </a>
    </h2>
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
    </div>
</div>
@stop

@section('js')

</script>

@stop