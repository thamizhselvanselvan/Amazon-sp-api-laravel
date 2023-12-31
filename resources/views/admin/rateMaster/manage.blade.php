@extends('adminlte::page')

@section('title', 'RateMaster')

@section('content_header')
<div class="row">
    <h1 class="m-0 text-dark col">Rate Master Management</h1>

</div>
<div class="row">
    <div class="col mt-4">
        <a href="{{ route('rateMaster.index') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-long-arrow-alt-left"></i> Back
        </a>
    </div>
</div>
<div class="row mt-3">
    <div class="col">
        <h1 class="m-0 text-dark text-center "> CSV Upload</h1>
    </div>
</div>
<!-- <div class="row">
    <div class="col">
        <a href="{{ route('invoice.index') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-long-arrow-alt-left"></i> Back
        </a>
    </div>
</div> -->
@stop

@section('css')
<style>
div.form-group {
    margin-bottom: 0rem;
}

</style>
@stop

@section('content')

<div class="loader d-none">
    <div class="sub-loader position-relative ">
        <div class="lds-hourglass"></div>
        <p>Loading...</p>
    </div>
</div>

<div class="row">
    <div class="col"></div>
    <div class="col-8">

        @if(session()->has('success'))
        <x-adminlte-alert theme="success" title="Success" dismissable>
            {{ session()->get('success') }}
        </x-adminlte-alert>
        @endif

        @if(session()->has('error'))
        <x-adminlte-alert theme="danger" title="Error" dismissable>
            {{ session()->get('error') }}
        </x-adminlte-alert>
        @endif

        <x-adminlte-alert theme="danger" title="Error" dismissable id="alert" style="display:none">
            Select export-rate file to upload in excel format.
        </x-adminlte-alert>

        <form class="row" id="multi-file-upload" method="POST" action="javascript:void(0)" accept-charset="utf-8"
            enctype="multipart/form-data">
            @csrf

            <div class="col-3"></div>

            <div class="col-6">
                <x-adminlte-input label="Select CSV File" name="files[]" id="files" type="file" />
                <label></label>
            </div>
            <div class="col-12">
                <div class="text-center">
                    <x-adminlte-button label="Upload" theme="primary" class="add_ btn-sm" id="upload_excel"
                        icon="fas fa-plus" type="submit" />
                </div>
            </div>
        </form>
    </div>
    <div class="col"></div>
</div>

@stop

@section('js')
<script src="{{ asset('js/app.js') }}"></script>
<script type="text/javascript">
$(function() {
    $("#alert").hide();
    $('#multi-file-upload').submit(function(e) {
        e.preventDefault();
        let files = $('#files')[0].files;
        let TotalFiles = $('#files')[0].files.length; //Total files
        if (TotalFiles < 1) {

            $('#alert').show();
            this.reset();
            return false;
        }

        let formData = new FormData(this);
        let count = 0;
        re = /(\.csv)$/i;
        $.each(files, function(index, elm) {

            if (re.exec(elm['name'])) {

                formData.append('files', elm[count]);

            } else {
                let file_extension = elm['name'].split('.').pop();
                alert("File extension ." + file_extension + " not supported ");
                return false;
            }
            ++count;
        });
        $.ajax({
            type: 'POST',
            url: "{{ url('/admin/rate-master/upload/csv')}}",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(response) {
                // console.log(response);
                alert('File upload successfully');
                if (response.success) {
                    getBack();
                }
            },
        });

        function getBack() {
            window.location.href = '/admin/rate-master';
        }
    });
});
</script>

@stop
