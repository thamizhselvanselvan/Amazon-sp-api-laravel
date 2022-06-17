@extends('adminlte::page')

@section('title', 'ASIN Upload')

@section('content_header')

<div class="row">
    <h1 class="m-0 text-dark">Upload ASIN</h1>
    <h2 class="text-right col">
    </h2>
</div>
@stop

@section('content')
<div class="row m-5">
    <div class="col-5">
        <form class="row" action="asin_save" method="POST" enctype="multipart/form-data">
        @csrf
            <label>Enter ASIN</label>
            <textarea class="form-control" rows="3" placeholder="Enter ASIN ..." name="textarea"></textarea>
            <div class="text-right m-2">
                <a href='asin_save'>
                    <x-adminlte-button label='Submit' class="btn-sm" theme="primary" icon="fas fa-file-upload" type="submit"/>
                </a>
            </div>
        </form>
    </div>
    <div class="col-6 text-center">
        <!-- <strong>Upload .TXT File</strong> -->
        <form class="row" action="add-bulk-asin" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="col-3"></div>

            <div class="col-6 text-left">
                <x-adminlte-input label="Upload ASIN txt File" name="asin" id="asin" type="file" />
            </div>

            <div class="col-12">
                <div class="text-center">
                    <x-adminlte-button label="Upload Asin" theme="primary" class="add_asin btn-sm" icon="fas fa-file-upload" type="submit" />
                </div>
            </div>
        </form>
    </div>
</div>

@stop

@section('js')
<script type="text/javascript">

</script>
@stop