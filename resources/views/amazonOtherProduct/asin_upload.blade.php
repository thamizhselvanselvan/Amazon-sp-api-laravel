@extends('adminlte::page')

@section('title', 'ASIN Upload')

@section('content_header')

<div class="row">
    <h1 class="m-0 text-dark">Upload ASIN</h1>
    <!-- <h2 class="text-right col">
        <a href='#'>
            <x-adminlte-button label='Export' class="" theme="primary" icon="fas fa-file-export" />
        </a>
    </h2> -->
</div>
@stop

@section('content')

<div class="col-sm-6">
    <div class="form-group">
        <div class="custom-control custom-radio select-text" data-type="text-box">
            <input class="custom-control-input" type="radio" id="txt-box" name="customRadio" checked>
            <label for="txt-box" class="custom-control-label">Text Box</label>
        </div>
        <div class="custom-control custom-radio select-text" data-type="file-box">
            <input class="custom-control-input" type="radio" id="txt-file" name="customRadio">
            <label for="txt-file" class="custom-control-label">Upload txt file</label>
        </div>
    </div>
</div>

<div class="row m-3 ">
    <div class="col-12 text-box-input">
        <form class="row" action="asin_save" method="POST" enctype="multipart/form-data">
            @csrf
            <label>Enter ASIN</label>
            <textarea class="form-control" rows="3" placeholder="Enter ASIN ..." name="textarea"></textarea>
            <div class="text-right m-2">
                <a href='asin_save'>
                    <x-adminlte-button label='Submit' class="btn-sm" theme="primary" icon="fas fa-file-upload" type="submit" />
                </a>
            </div>
        </form>
    </div>

    <div class="col-12 text-center txt-file-upload d-none">
        <!-- <strong>Upload .TXT File</strong> -->
        <form class="row" action="add-bulk-asin" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="col-3"></div>
            <div class="col-6 text-left">
                <x-adminlte-input label="Upload ASIN txt File" name="asin" id="asin" type="file" />
            </div>
            <div class="col-12">
                <div class="text-center">
                    <x-adminlte-button label="Upload File" theme="primary" class="add_asin btn-sm" icon="fas fa-file-upload" type="submit" />
                </div>
            </div>
        </form>
    </div>
</div>

@stop

@section('js')
<script type="text/javascript">
    $(".select-text").on('click', function() {
        let self = $(this);
        let val = self.attr('data-type');
        let text_box = $(".text-box-input");
        let file_box = $(".txt-file-upload");


        if(val == "text-box") {

            text_box.removeClass("d-none");
            file_box.addClass("d-none");

        } else {
            text_box.addClass("d-none");
            file_box.removeClass("d-none");
        }
    })
</script>
@stop