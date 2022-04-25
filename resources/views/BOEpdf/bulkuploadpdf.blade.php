@extends('adminlte::page')

@section('title', 'PDF Master')

@section('content_header')

<div class="row mt-3">
  <div class="col">
    <h1 class="m-0 text-dark text-center ">Bulk BOE Upload</h1>
  </div>
</div>
<!-- <div class="row">
  <div class="col-6">
    <a href="/BOE/index" class="btn btn-primary">
      <i class="fas fa-long-arrow-alt-left"></i> Back
    </a>
  </div>
</div> -->

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

    <x-adminlte-alert theme="danger" title="Error" dismissable id="alert">
    You can upload miximum 200 BOE at a time.
    </x-adminlte-alert>

    <form class="row" id="multi-file-upload" method="POST" action="javascript:void(0)" accept-charset="utf-8" enctype="multipart/form-data">
      @csrf

      <div class="col-3"></div>

      <div class="col-6">
        <x-adminlte-input label="Select BOE Files" name="files[]" id="files" type="file" multiple />
      </div>

      <div class="col-3"></div>

      <div class="col-12">
        <div class="text-center">
          <x-adminlte-button label="Upload BOE" theme="primary" class="add_ btn-sm" id="upload_pdf" icon="fas fa-plus" type="submit" />
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
      if(TotalFiles >200){

        $('#alert').show();
        this.reset();
        return false;
      }

      let formData = new FormData(this);
      $.each(files, function(index, elm) {

        formData.append('files', elm[index]);
      });

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
        // error: function(data) {
        //     alert(data.responseJSON.errors.files[0]);
        //     console.log(data.responseJSON.errors);
        // }
      });
    });
  });
</script>

@stop