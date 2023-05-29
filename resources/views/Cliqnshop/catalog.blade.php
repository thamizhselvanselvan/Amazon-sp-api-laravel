@extends('adminlte::page')
@section('title', 'Catalog & Price')

@section('css')
<link rel="stylesheet" href="/css/styles.css">
<style>
    footer {
        display: none;
    }
</style>
@stop

@section('content_header')

<div class="row">
    <h1 class="m-0 text-dark">Cliqnshop Catalog </h1>
</div>

<div class="row">
    <div style="margin-top: 1.0rem;">
        <div class="col d-flex">

            <!-- <h2 class="ml-2">
                <x-adminlte-button label="Cliqnshop Catalog Export" theme="primary" class="btn-sm" icon="fas fa-file-export" id="exportcliqnshopCatalog" />
            </h2>
            <h2 class="ml-2">
                <x-adminlte-button label="Download Cliqnshop Catalog" theme="primary" class="btn-sm" icon="fas fa-download" id="catalogcliqnshopdownload" data-toggle="modal" data-target="#downloacliqdModal" />
            </h2> -->
            <h2 class="ml-2">
                <x-adminlte-button label="Upload New ASIN" theme="info" class="btn-sm" icon="fas fa-upload" id="new_asin" data-toggle="modal" data-target="#cliqnshop_new_asin_modal" />
            </h2>
            {{-- <h2 class="ml-2">
                <x-adminlte-button label="Download Upload ASIN Catalog" theme="info" class="btn-sm" icon="fas fa-download" id="new_asin_cat" data-toggle="modal" data-target="#uploaded_asin_catalog" />
            </h2> --}}

            <h2 class="ml-2">
                <x-adminlte-button label="uploaded asin exporter" theme="info" class="btn-sm btn-danger" icon="fas fa-download" id="btn_export_lister" data-toggle="modal" data-target="#uploaded_asin_exporter" />
            </h2>

            <h2 class="ml-2">
                <x-adminlte-button label="Asin Updater By CSV" theme="info" class="btn-sm btn-warning" icon="fas fa-download" id="btn_export_updater" data-toggle="modal" data-target="#exported_asin_updater_modal" />
            </h2>
        </div>

        <!--  download files from asin from all Download -->
        <div class="modal" id="downloacliqdModal">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Download Cliqnshop Catalog </h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body catalogcliqnshop">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!--  download files from asin Import -->
        <div class="modal" id="uploaded_asin_catalog">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Download Uploaded asin Catalog </h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body new_upload_catalog">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!--  asin exporter  --start -->
            <div class="modal" id="uploaded_asin_exporter">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Asin Exporter </h4>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body file_lister">
                            
                            <div class="card card-info card-tabs">
                                <div class="card-header p-0 pt-1">
                                    <ul class="nav nav-tabs" id="custom-tabs-five-tab" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" id="custom-tabs-file-lister-csv-tab" data-toggle="pill"
                                                href="#custom-tabs-file-lister-csv" role="tab" aria-controls="custom-tabs-file-lister-csv"
                                                aria-selected="true">via CSV</a>
                                        </li>                                        
                                        <li class="nav-item">
                                            <a class="nav-link" id="custom-tabs-file-lister-textarea-tab" data-toggle="pill" href="#custom-tabs-file-lister-textarea"
                                                role="tab" aria-controls="custom-tabs-file-lister-textarea" aria-selected="false">via Text-Area</a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="card-body">
                                    <div class="tab-content" id="custom-tabs-five-tabContent">
                                        <div class="tab-pane fade active show" id="custom-tabs-file-lister-csv" role="tabpanel"
                                            aria-labelledby="custom-tabs-file-lister-csv-tab">
                                            
                                        </div>
                                        
                                        <div class="tab-pane fade" id="custom-tabs-file-lister-textarea" role="tabpanel"
                                            aria-labelledby="custom-tabs-file-lister-textarea-tab">
                                             
                                        </div>
                                    </div>
                                </div>
                            
                            </div>
                            
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        <!--  asin exporter  --end  -->

        
        <!--  exported asin updater modal  --start -->
            <div class="modal fade" id="exported_asin_updater_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel"><b>Cliqnshp ASIN Updater By CSV</b></h5>
                            <button type="button" class="close btn-sm" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="fasle">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body " style="font-size:15px">
                            <div id="warning" class="alert alert-warning alert-dismissible fade show" role="alert">
                                 Upload the data in csv format only.
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <form class="row" id="multi-file-upload" method="POST" action="{{ route('cliqnshop.catalog.asin.export.list.update') }}" accept-charset="utf-8" enctype="multipart/form-data">
                                @csrf
                               
                                <div class="col-12">
                                    <x-adminlte-input label="Choose CSV File" name="cliqnshop_csv" id="files" type="file" />
                                </div>
                                <div class="col">                                
                                        <x-adminlte-button label="Upload" theme="primary" class="add_ btn-sm" icon="fas fa-upload" type="submit" id="order_upload" />
                                </div>
                            </form>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fas fa-window-close" aria-hidden="true"></i> Close</button>
                        </div>
                    </div>
                </div>
            </div>
         <!--  exported asin updater modal  --end -->


        <div class="modal fade" id="cliqnshop_new_asin_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"><b>Cliqnshp Catalog Operations</b></h5>
                        <button type="button" class="close btn-sm" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="fasle">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body " style="font-size:15px">

                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home-tab-pane" type="button" role="tab" aria-controls="home-tab-pane" aria-selected="true">
                                    Import ASIN's From CSV
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile-tab-pane" type="button" role="tab" aria-controls="profile-tab-pane" aria-selected="false">
                                    Import From Text Area
                                </button>
                            </li>
                        </ul>

                        <!-- CSV Import Tab -->
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="home-tab-pane" role="tabpanel" aria-labelledby="home-tab" tabindex="0">
                                <div id="warning" class="alert alert-warning alert-dismissible fade show" role="alert">
                                    please download <strong>CSV Templete</strong> and upload the data in csv format only.
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>

                                <div class="modal-body">
                                    <form class="row" id="multi-file-upload" method="POST" action="{{ route('cliqnshop.catalog.csv.import') }}" accept-charset="utf-8" enctype="multipart/form-data">
                                        @csrf
                                        <div class="col-12">
                                            <x-adminlte-select name="image" label="Select Country" name="country">
                                                <option value=''>Select Country</option>
                                                @foreach ($countrys as $country)
                                                <option value="{{ $country->siteid }}">{{$country->code }}</option>
                                                @endforeach
                                            </x-adminlte-select>
                                        </div>
                                        <div class="col-12">
                                            <x-adminlte-input label="Choose CSV File" name="cliqnshop_csv" id="files" type="file" />
                                        </div>
                                        <div class="col">
                                            <a href="{{ route('cliqnshop.catalog.csv.templete') }}">
                                                <x-adminlte-button label="Download Template" theme="info" icon="fas fa-file-download" class="btn-sm ml-2" />
                                                <x-adminlte-button label="Upload" theme="primary" class="add_ btn-sm" icon="fas fa-upload" type="submit" id="order_upload" />
                                            </a>
                                        </div>
                                    </form>
                                </div>

                            </div>


                            <!--Text area Tab -->
                            <div class="tab-pane fade" id="profile-tab-pane" role="tabpanel" aria-labelledby="profile-tab" tabindex="0">
                                <div id="warning" class="alert alert-warning alert-dismissible fade show" role="alert">

                                    <strong> Text Area </strong> Tab (10 ASIn's Acceptet At a time)
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form action="{{ Route('cliqnshop.catalog.textarea.import') }}" method="POST" id="admin_user">
                                    @csrf

                                    <div class="row">
                                        <div class="col-12">
                                            <x-adminlte-select name="image" label="Select Country" name="text_country">
                                                <option value=''>Select Country</option>
                                                @foreach ($countrys as $country)
                                                <option value="{{ $country->siteid }}">{{$country->code }}</option>
                                                @endforeach
                                            </x-adminlte-select>
                                        </div>
                                        <div class="col-12" id="order_id">
                                            <div class="form-group">
                                                <label>Enter ASIN's:</label>
                                                <div class="autocomplete" style="width:760;">
                                                    <textarea name="order_ids_text" rows="5" placeholder="Add Enter ASIN's here..." id="" type=" text" autocomplete="off" class="form-control up_asin_sync"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-3">
                                            <x-adminlte-button label="Submit" theme="primary" icon="fas fa-file-upload" id="upload_sync" class="btn-sm upload_asin_btn" type="submit" />
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fas fa-window-close" aria-hidden="true"></i> Close</button>
                    </div>
                </div>
            </div>
        </div>



        <!-- //old Modal for cliqnshop Catalog Import -->
        <!-- <div class="modal fade" id="cliqnshop_new_asin_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="new_asin">Add New Asins To Cliqnshop</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div id="warning" class="alert alert-warning alert-dismissible fade show" role="alert">
                        please download <strong>CSV Templete</strong> and upload the data in csv format only.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form class="row" id="multi-file-upload" method="POST" action="{{ route('cliqnshop.catalog.csv.import') }}" accept-charset="utf-8" enctype="multipart/form-data">
                            @csrf
                            <div class="col-12">
                                <x-adminlte-select name="image" label="Select Country" name="country">
                                    <option value=''>Select Country</option>
                                    @foreach ($countrys as $country)
                                    <option value="{{ $country->siteid }}">{{$country->code }}</option>
                                    @endforeach
                                </x-adminlte-select>
                            </div>
                            <div class="col-12">
                                <x-adminlte-input label="Choose CSV File" name="cliqnshop_csv" id="files" type="file" />
                            </div>
                            <div class="col">
                                <a href="{{ route('cliqnshop.catalog.csv.templete') }}">
                                    <x-adminlte-button label="Download Template" theme="info" icon="fas fa-file-download" class="btn-sm ml-2" />
                                    <x-adminlte-button label="Upload" theme="primary" class="add_ btn-sm" icon="fas fa-upload" type="submit" id="order_upload" />
                                </a>
                            </div>
                            <div class="col-3 text-right">
                                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fas fa-window-close" aria-hidden="true"></i> Close</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div> -->
    </div>
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
        <div class="alert_display">
            @if ($message = Session::get('error'))
            <div class="alert alert-danger alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $message }}</strong>
            </div>
            @endif
        </div>
    </div>
</div>

@stop
<!-- <script src="https://js.pusher.com/7.2/pusher.min.js"></script> -->
<script>
    // Enable pusher logging - don't include this in production
    // Pusher.logToConsole = true;

    // var pusher = new Pusher('ea37c18af4de51c2ea0a', {
    //     cluster: 'ap2'
    // });

    


    // var channel = pusher.subscribe('channel');
    // channel.bind('.event', function(data) {
    //     alert(JSON.stringify(data));
    // });
</script>




@section('js')
<script type="text/javascript">
    $('#exportcliqnshopCatalog').on('click', function() {
        window.location.href = '/catalog/cliqnshop/export';
    });

    $('#catalogcliqnshopdownload').click(function() {

        $.ajax({
            url: "/catalog/cliqnshop/get-file",
            method: "GET",
            data: {
                "catalog": "Cliqnshop/catalog",
                "_token": "{{ csrf_token() }}",
            },
            success: function(response) {

                if (response == '') {
                    $('.catalogcliqnshop').empty();
                    $('.catalogcliqnshop').append('File Downloading..');
                    return false;
                } else {
                    $('.catalogcliqnshop').empty();
                    let files = '';
                    $.each(response, function(index, result) {

                        files += "<li class='p-0 m-0'>";
                        files += "<a href='/catalog/cliqnshop/download/" + index + "'>" +
                            index + '&nbsp; ' + "</a>";
                        files += result;

                        files += "</li>";

                    });
                    $('.catalogcliqnshop').append(files);

                }

            },
            error: function(response) {
                console.log(response);
            },
        });
    });


    // catalog exporter window popup --start
        

        
        $('#btn_export_lister').click(function() {
            csvExportedFileLister();
        });
        $('#custom-tabs-file-lister-csv-tab').click(function() {
            csvExportedFileLister();
        });
        $('#custom-tabs-file-lister-textarea-tab').click(function() {
            textareaExportedFileLister();
        });

         function csvExportedFileLister() 
         {
            $('#custom-tabs-file-lister-csv').empty();
            let loader = `<div class="overlay-wrapper" style="min-height: 25vh!important;">
                            <div class="overlay "><i class="fas fa-3x fa-sync-alt fa-spin"></i>
                                <div class="text-bold pt-2">Loading...</div>
                            </div>                                
                          </div>`;
            $('#custom-tabs-file-lister-csv').prepend(loader)
            $.ajax({
                url: "{{route('cliqnshop.catalog.asin.export.list')}}",
                method: "GET",
                data: {
                    "catalog": "Cliqnshop/upload/asin/export",
                    "_token": "{{ csrf_token() }}",
                },
                success: function(response) {

                    if (response == '') {
                        $('#custom-tabs-file-lister-csv').empty();
                        $('#custom-tabs-file-lister-csv').append('Please Upload The File And Wait For Fe Minuts Catalog Is Exporting..');
                        return false;
                    } else {
                        $('#custom-tabs-file-lister-csv').empty();
                        let files = '';
                        $.each(response, function(index, result) {
                            console.log(result);
                            files += "<li class='p-0 m-0'>";
                            files += "<a href='/cliqnshop/catalog/asin/export/list/download/" + index + "'>" +
                                index + '&nbsp; ' + "</a>";
                            files += ' created at <span class="text-warning">'+ result + '</span>';

                            files += "</li>";

                        });
                        $('#custom-tabs-file-lister-csv').append(files);

                    }

                },
                error: function(response) {
                    console.log(response);
                    response.status == 500
                    {
                        $('#custom-tabs-file-lister-textarea').empty();
                        $('#custom-tabs-file-lister-textarea').append('Please Upload The File And Wait For Few Minutes ,  Catalog Is Exporting..');
                    }
                },
            });
         }
         
         function textareaExportedFileLister() 
         {
            $('#custom-tabs-file-lister-textarea').empty();
            let loader = `<div class="overlay-wrapper" style="min-height: 25vh!important;">
                            <div class="overlay "><i class="fas fa-3x fa-sync-alt fa-spin"></i>
                                <div class="text-bold pt-2">Loading...</div>
                            </div>                                
                          </div>`;
            $('#custom-tabs-file-lister-textarea').prepend(loader)
            $.ajax({
                url: "{{route('cliqnshop.catalog.asin.export.list')}}",
                method: "GET",
                data: {
                    "catalog": "Cliqnshop/upload/asin/exported_from_textarea",
                    "_token": "{{ csrf_token() }}",
                },
                success: function(response) {

                    if (response == '') {
                        $('#custom-tabs-file-lister-textarea').empty();
                        $('#custom-tabs-file-lister-textarea').append('Please Upload The File And Wait For Fe Minuts Catalog Is Exporting..');
                        return false;
                    } else {
                        $('#custom-tabs-file-lister-textarea').empty();
                        let files = '';
                        $.each(response, function(index, result) {
                            console.log(result);
                            files += "<li class='p-0 m-0'>";
                            files += "<a href='/cliqnshop/catalog/asin/text-area-export/list/download/" + index + "'>" +
                                index + '&nbsp; ' + "</a>";
                            files += ' created at <span class="text-warning">'+ result + '</span>';

                            files += "</li>";

                        });
                        $('#custom-tabs-file-lister-textarea').append(files);

                    }

                },
                error: function(response) {
                    console.log(response);
                    response.status == 500
                    {
                        $('#custom-tabs-file-lister-textarea').empty();
                        $('#custom-tabs-file-lister-textarea').append('Please Upload The File And Wait For Fe Minutes, Catalog Is Exporting..');
                    }
                },
            });
         }  

    // catalog exporter window popup  --end


    $('#new_asin_cat').click(function() {

        $.ajax({
            url: "{{route('uploaded.asin.catalog.export.cliqnshop')}}",
            method: "GET",
            data: {
                "catalog": "Cliqnshop/\imported_cat",
                "_token": "{{ csrf_token() }}",
            },
            success: function(response) {

                if (response == '') {
                    $('.new_upload_catalog').empty();
                    $('.new_upload_catalog').append('Please Upload The File And Wait For Fe Minuts Catalog Is Exporting..');
                    return false;
                } else {
                    $('.new_upload_catalog').empty();
                    let files = '';
                    $.each(response, function(index, result) {

                        files += "<li class='p-0 m-0'>";
                        files += "<a href='/uploaded/catalog/cliqnshop/download/" + index + "'>" +
                            index + '&nbsp; ' + "</a>";
                        files += result;

                        files += "</li>";

                    });
                    $('.new_upload_catalog').append(files);

                }

            },
            error: function(response) {
                console.log(response);
            },
        });
    });
</script>
@stop