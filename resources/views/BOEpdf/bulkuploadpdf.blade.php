@extends('adminlte::page')

@section('title', 'PDF Master')

@section('content_header')

<div class="row mt-3">
    <div class="col">
        <h1 class="m-0 text-dark text-center ">Bulk PDF Upload</h1>
    </div>
</div>
<div class="row">
    <div class="col-6">
        <a href="/BOE/index" class="btn btn-primary">
            <i class="fas fa-long-arrow-alt-left"></i> Back
        </a>
    </div>
</div>

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

        <form class="row" id="multi-file-upload" method="POST"  action="javascript:void(0)" accept-charset="utf-8" enctype="multipart/form-data">
            @csrf

            <div class="col-3"></div>

            <div class="col-6">
                <x-adminlte-input label="Upload PDF" name="files[]" id="files" type="file" multiple />
            </div>

            <div class="col-3"></div>

            <div class="col-12">
                <div class="text-center">
                    <x-adminlte-button label="Upload PDF" theme="primary" class="add_" id="upload_pdf" icon="fas fa-plus" type="submit" />
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

        $('#multi-file-upload').submit(function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            let TotalFiles = $('#files')[0].files.length; //Total files
            let files = $('#files')[0];
            for (let i = 0; i < TotalFiles; i++) {
                formData.append('files' + i, files.files[i]);
            }
            formData.append('TotalFiles', TotalFiles);
            $.ajax({
                type: 'POST',
                url: "{{ url('BOE/bulk-upload')}}",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: (data) => {
                    this.reset();
                    alert('Files has been uploaded');
                },
                error: function(data) {
                    alert(data.responseJSON.errors.files[0]);
                    console.log(data.responseJSON.errors);
                }
            });
        });
    });

    $(function () {
  'use strict';

  // Initialize the jQuery File Upload widget:
  $('#fileupload').fileupload({
    // Uncomment the following to send cross-domain cookies:
    //xhrFields: {withCredentials: true},
    url: 'server/php/'
  });

  // Enable iframe cross-domain access via redirect option:
  $('#fileupload').fileupload(
    'option',
    'redirect',
    window.location.href.replace(/\/[^/]*$/, '/cors/result.html?%s')
  );

  if (window.location.hostname === 'blueimp.github.io') {
    // Demo settings:
    $('#fileupload').fileupload('option', {
      url: '//jquery-file-upload.appspot.com/',
      // Enable image resizing, except for Android and Opera,
      // which actually support image resizing, but fail to
      // send Blob objects via XHR requests:
      disableImageResize: /Android(?!.*Chrome)|Opera/.test(
        window.navigator.userAgent
      ),
      maxFileSize: 999000,
      acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i
    });
    // Upload server status check for browsers with CORS support:
    if ($.support.cors) {
      $.ajax({
        url: '//jquery-file-upload.appspot.com/',
        type: 'HEAD'
      }).fail(function () {
        $('<div class="alert alert-danger"></div>')
          .text('Upload server currently unavailable - ' + new Date())
          .appendTo('#fileupload');
      });
    }
  } else {
    // Load existing files:
    $('#fileupload').addClass('fileupload-processing');
    $.ajax({
      // Uncomment the following to send cross-domain cookies:
      //xhrFields: {withCredentials: true},
      url: $('#fileupload').fileupload('option', 'url'),
      dataType: 'json',
      context: $('#fileupload')[0]
    })
      .always(function () {
        $(this).removeClass('fileupload-processing');
      })
      .done(function (result) {
        $(this)
          .fileupload('option', 'done')
          // eslint-disable-next-line new-cap
          .call(this, $.Event('done'), { result: result });
      });
  }
});
</script>

@stop