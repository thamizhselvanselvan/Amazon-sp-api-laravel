@extends('adminlte::page')

@section('title', 'PDF Master')

@section('content_header')

<div class="row mt-3">
  <div class="col">
    <h1 class="m-0 text-dark text-center ">Bulk BOE Upload</h1>
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
  <div class="col">

    <div class="lists">

    </div>

  </div>
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
      Upload minimun 1 and miximum 150 BOE at a time.
    </x-adminlte-alert>

    <form class="row" id="multi-file-upload" method="POST" action="javascript:void(0)" accept-charset="utf-8" enctype="multipart/form-data">
      @csrf

      <div class="col-3"></div>

      <div class="col-6">
        <x-adminlte-input label="Select BOE Files" name="files[]" id="files" type="file" multiple />
        <label> Upload up to 150 BEO at a time</label>
      </div>
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
      $("#upload_pdf").prop("disabled", true);
      $("body").css("cursor", "progress");
      e.preventDefault();
      let files = $('#files')[0].files;
      let TotalFiles = $('#files')[0].files.length; //Total files
      if (TotalFiles > 150 || TotalFiles < 1) {

        $('#alert').show();
        this.reset();
        $("body").css("cursor", "default");
        $("#upload_pdf").removeAttr('disabled');
        return false;
      }

      let formData = new FormData(this);
      let count = 0;
      re = /(\.pdf)$/i;
      $.each(files, function(index, elm) {

        if (re.exec(elm['name'])) {
          formData.append('files', elm[count]);
        } else {
          let file_extension = elm['name'].split('.').pop();
          alert("File extension ." + file_extension + " not supported ");
        }
        ++count;
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
          $("#upload_pdf").removeAttr('disabled');
          $("body").css("cursor", "default");

          console.log(data);

          if (data.hasOwnProperty("error")) {
            alert('We are unabel to process some BOE, please check below list');
            let lists = $(".lists");
            let html_array = "<h4 class='font-weight-bold'> Failed BOE</h4>";

            $.each(data.error, function(index, value) {

              html_array += "<li>" + value + "</li>";

            });

            lists.html(html_array);

          } else {
            alert('All Files has been uploaded');
            window.location.href = 'index';

          }

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