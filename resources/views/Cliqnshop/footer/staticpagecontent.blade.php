@extends('adminlte::page')

@section('title', 'Footer')

@section('css')
<link rel="stylesheet" href="/css/styles.css">
@stop

@section('js')

<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

<script>
         
    
    $(document).ready(function(){ 
        
    function makeTinyMceEditor() {
            tinymce.init({
            selector: '#myContentTextarea',
           
            placeholder: 'Enter the page content here',
            height: 400,
            plugins: 'code styles table  anchor autolink fullscreen forecolor backcolor emoticons  help insertdatetime emoticons searchreplace directionality code advlist lists wordcount nonbreaking  preview ',  // required by the code menu item
            toolbar: 'fontfamily fontsize blocks | forecolor backcolor removeformat  | bold italic underline strikethrough | link  blockquote codesample | align bullist numlist | code | pagebreak | charmap emoticons | fullscreen  preview save print ',
            autosave_retention: true,
        });
    }

    makeTinyMceEditor();

        
    var $selects = $('#site, #section').on('change', function(){

        if( $('#site').val() && $('#section').val()) 
        {
            var formData = {
                                _token : '<?php echo csrf_token() ?>',
                                site : $('#site').val(),
                                section : $('#section').val(),
                            };
            jQuery.ajax({  
                url: 'getstaticpagecontent',  
                type: 'POST',  
                data: formData,
                success: function(data) { 
                    
                    if (data.content === '') {
                        tinymce.get('myContentTextarea').setContent('');
                        
                        } else {

                            tinymce.get('myContentTextarea').setContent(data.content);
                        }                           
                } ,
                error: function (jqXHR, exception) {
                    var msg = '';
                    if (jqXHR.status === 0) {
                        msg = 'Not connect.\n Verify Network.';
                    } else if (jqXHR.status == 404) {
                        msg = 'Requested page not found. [404]';
                    } else if (jqXHR.status == 500) {
                        tinymce.get('myContentTextarea').setContent('');
                        msg = 'Internal Server Error [500].';
                    } else if (exception === 'parsererror') {
                        msg = 'Requested JSON parse failed.';
                    } else if (exception === 'timeout') {
                        msg = 'Time out error.';
                    } else if (exception === 'abort') {
                        msg = 'Ajax request aborted.';
                    } else {
                        msg = 'Uncaught Error.\n' + jqXHR.responseText;
                    }
                    console.log(msg);
                }, 
            });
            
        }
        else
        {
            tinymce.get('myContentTextarea').setContent('');
        }
    })
        
                
            
    });  

</script>

@stop

@section('content_header')
<div class="row">
    <div class="col=6"></div>

    <div class="col text-center">
        <h3>Cliqnshop Static Page Section</h3>
    </div>
</div>

@stop

@section('content')

<form action="/cliqnshop/staticpagecontent" method="POST">



    @csrf
    
    <div class="container">
        <div class="row ">

            {{-- success alert messager --start --}}
            <div class="col-md-12">
                @if (session()->has('success'))
                <div class="alert alert-success" role="alert">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    {{ session()->get('success') }}
                </div>
                @endif
            </div>
            {{-- success alert messager --end --}}

            <div class="col-md-6">
               
                    <x-adminlte-select name="site" id="site" label="Select Site" class="form-control-sm">
                        <option value="" selected>Select Site</option>
                        <option value="in">India</option>
                        <option value="uae">UAE</option>
                    </x-adminlte-select>
                
            </div>
            <div class="col-md-6">
                <x-adminlte-select name="section" id="section" label="Select Section" class="form-control-sm">
                    <option value="" selected>Select the page</option>
                    <option value="terms_and_conditions">Terms And Conditions</option>
                    <option value="privacy_policy">Privacy Policy</option>
                    <option value="return _and_refund_policy">Return & Refund Policy</option>
                    <option value="imprint">Imprint</option> 
                    <option value="disclaimer">Disclaimer</option>
                    <option value="warranty">Warranty</option>
                   
                </x-adminlte-select>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <textarea  label="Enter Page Content" name="content" id="myContentTextarea" type="text" placeholder="Enter Page Content" >
                    {{old('content')}}
                </textarea>

            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <br>
                <x-adminlte-button class="btn-flat btn-sm" type="submit" label="Submit" theme="success" icon="fas fa-lg fa-save"  />

            </div>
        </div>

    </div>
   
        
            
           
           
    
</form>
@stop

