@extends('adminlte::page')

@section('title', 'Site Information')

@section('css')
<link rel="stylesheet" href="/css/styles.css">
@stop

@section('content_header')
<div class="row">
    <div class="col=6"></div>

    <div class="col text-center">
        <h3>Cliqnshop Site information </h3>
    </div>
</div>

@stop

@section('content')

<form action="/cliqnshop/footercontent" method="POST">
    @csrf
    <center>
        <div class="form-group w-50">

            <div id="info">

                @if (session()->has('success'))
                <div class="alert alert-success" role="alert">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    {{ session()->get('success') }}
                </div>
                @endif

               
            </div>


            <x-adminlte-select name="site" id="site" label="Select Site" >
                <option value="" selected>Select Site</option>
                <option value="in">India</option>
                <option value="uae">UAE</option>
            </x-adminlte-select>
            <x-adminlte-select name="section" id="section" label="Select Inforamtion Type">
                <option value="" selected>Select Inforamtion Type</option>
                <option value="Call Us">Contact Phone Number</option>
                <option value="Email for Us">Contact Email Address</option>
                <option value="Facebook">Facebook link</option>
                <option value="Twitter">Twitter link</option>
                <option value="Instagram">Instagram link</option>
                <option value="Youtube">Youtube link</option>
                <option value="shop_address">Shop Address Detail</option>
                <option value="google_map_src">Google Maps Src Link</option>
            </x-adminlte-select>
            <div>
                <x-adminlte-textarea label="Enter Content" name="content" id="content" rows=5 type="text" placeholder="Enter Content" />
            </div>
            <x-adminlte-button class="btn-flat" type="submit" label="Submit" theme="success" icon="fas fa-lg fa-save" />
    </center>
</form>
@stop

@section('js')


<script>
         
    
    $(document).ready(function(){ 

        let msgs = {
                        'Call Us': 'Provide Us shop Phone number ',
                        'Email for Us': 'Provide Us shop Email Address  ',
                        'Facebook': 'Goto shop <a target="_blank" href="https://www.facebook.com/" >Facebook </a> page , And copy the Profile URL .',
                        'Twitter':'Goto shop <a target="_blank" href="https://twitter.com/" >Twitter </a> page , And copy the Profile URL .',
                        'Instagram':'Goto shop <a target="_blank" href="https://www.instagram.com/" >Instagram </a> page , And copy the Profile URL .',
                        'Youtube':'Goto shop <a target="_blank" href="https://www.youtube.com/" >Youtube </a> page , And copy the Profile URL .',
                        'shop_address':' Provide Us Shop Address Detais ',
                        'google_map_src':`  <div style="text-align: initial">
                                            <p> Shop  Location Using Goople Map Exist on Contact Page   </p>
                                            <ul>
                                            <li>Step 1 : Open <a target="_blank" href="https://maps.google.com/" > Google Maps </a> and Select the Shop Location </a></li> 
                                            <li>Step 2 :  Cick On  Share Option </li>  
                                            <li>Step 3 :  Click On Embed Maps And Copy "SRC" and Paste Here </li></ul></div>`     
                                        ,
                    };

        function showTipAlert(section)
        {
            
            var alertBlock =  `<div id='${section.replaceAll(" ","_")}' class="alert alert-warning alert-dismissible fade show" role="alert">
                                <strong>Tip!</strong>
                                <p>${msgs[section]}</p>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                </div>`;
            $('#info ').prepend($(alertBlock));
        }
        function removeTipAlert()
        {
            
                    for (const key of Object.keys(msgs)) 
                    {
                        
                        let section_id = '#'+key.replaceAll(" ","_"); 
                        $(section_id).remove();
                    }

        }
        removeTipAlert()
        
    var $selects = $('#site, #section').on('change', function(){

        if( $('#site').val() && $('#section').val()) 
        {


            removeTipAlert();
            showTipAlert($('#section').val());
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
                            $('#content').val('');
                        
                        } else {
                            $('#content').val(data.content);
                            
                        }                           
                } ,
                error: function (jqXHR, exception) {
                    var msg = '';
                    if (jqXHR.status === 0) {
                        msg = 'Not connect.\n Verify Network.';
                    } else if (jqXHR.status == 404) {
                        msg = 'Requested page not found. [404]';
                    } else if (jqXHR.status == 500) {
                         $('#content').val('');
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
             $('#content').val('');
        }
    })
        
                
            
    });  

</script>


@stop