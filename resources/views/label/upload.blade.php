@extends('adminlte::page')

@section('title', 'PDF Master')

@section('content_header')

<div class="row mt-3">
    <div class="col">
        <h1 class="m-0 text-dark text-center ">Label Excel Upload</h1>
    </div>
</div>
<div class="row">
    <div class="col">
        <a href="{{ route('label.manage') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-long-arrow-alt-left"></i> Back
        </a>
    </div>
</div>

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
            Please Select Label Excel.
        </x-adminlte-alert>

        <form class="row" id="multi-file-upload" method="POST" action="javascript:void(0)" accept-charset="utf-8" enctype="multipart/form-data">
            @csrf

            <div class="col-3"></div>

            <div class="col-6">
                <x-adminlte-input label="Select Excel" name="files[]" id="files" type="file" />
                <label></label>
            </div>
            <div class="col-12">
                <div class="text-center">
                    <x-adminlte-button label="Upload" theme="primary" class="add_ btn-sm" id="upload_excel" icon="fas fa-plus" type="submit" />
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

            //   $("#upload_excel").prop("disabled", true);
            //   $("body").css("cursor", "progress");
            e.preventDefault();
            let files = $('#files')[0].files;
            let TotalFiles = $('#files')[0].files.length; //Total files
            if (TotalFiles < 1) {

                $('#alert').show();
                this.reset();
                // $("body").css("cursor", "default");
                // $("#upload_excel").removeAttr('disabled');
                return false;
            }

            let formData = new FormData(this);
            let count = 0;
            re = /(\.xlsx)$/i;
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
                url: "{{ url('label/upload/excel')}}",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response) {
                    alert(response.success);
                    if (response.success) {
                        getBack();
                    }
                },
                error: function(response) {
                    alert('error');
                }
            });

            function getBack() {
                window.location.href = '/label/manage'
            }
        });
    });
</script>

@stop