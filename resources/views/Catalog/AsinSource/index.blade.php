@extends('adminlte::page')

@section('title', 'ASIN Source')

@section('css')
<!-- <link rel="stylesheet" href="/css/styles.css"> -->
<style type="text/css">
    #error{
        margin-top : -20px;
    }
</style>

@stop

@section('content_header')
<div class="row">
    <div class="col">

        <h1 class="m-0 text-dark">ASIN Source</h1>
    </div>
    <div class="col ">

        <h2 class="mb-4 float-right">

            <a href="import-bulk-asin">
                <x-adminlte-button label="Asin Bulk Import" theme="primary" icon="fas fa-file-import" class="btn-sm" />
            </a>
            <!-- <a href="{{route('catalog.asin.export')}}">
                <x-adminlte-button label="Asin Export" theme="primary" icon="fas fa-file-export" class="btn-sm " />
            </a>

            <x-adminlte-button label="Download Asin" theme="primary" icon="fas fa-file-download" data-toggle="modal"
                data-target="#exampleModal" class="btn-sm"></x-adminlte-button> -->

            <a href="{{ route('catalog.download.template') }}">
                <x-adminlte-button label="Download Template" theme="primary" icon="fas fa-file-download"
                    id="exportUniversalTextiles" class="btn-sm" />
            </a>

            <x-adminlte-button label="Asin Truncate" theme="primary" icon="fas fa-trash text-danger"
                class="btn-sm" data-toggle="modal" data-target="#asinTruncate"></x-adminlte-button>

            <div class="modal fade" id="asinTruncate" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">ASIN Table Truncate</h5>
                            <button type="button" class="close btn-sm" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body" style="font-size:15px">
                            <h5>Select Source</h5><br>
                            <form action="{{ route('catalog.asin.source.truncate') }}">
                                <div class="row">
                                    <div class="col-2">
                                        <label for="IN">IN</label>
                                        <input type="checkbox" name="source[]" value="IN" >
                                    </div>
                                    <div class="col-2">
                                        <label for="US">US</label>
                                        <input type="checkbox" name="source[]" value="US" >
                                    </div>
                                    
                                </div><br>
                                <div class="col-12 float-left">
                                    <x-adminlte-button label="Truncate" theme="danger" class="btn btn-sm truncate" icon="fas fa-trash " type="submit" />
                                </div>
                            </form>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-danger" data-dismiss="modal"  >Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </h2>
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
    </div>
</div>
<div class="row">
    <div class="col-2"></div>
    <div class="col-8 ">
        <div class="card ">
            <div class="card-body ">
                <label for="Select Source" class ="mt-0">Select Source</label><br>
                <div class="row ">
                    <div class="col-1">
                        <input type="radio" class="Asin-source" name="source" value="IN"  />
                        <label for="IN">IN</label>
                    </div>
                    <div class="col-1">
                        <input type="radio" class="Asin-source" name="source" value="US"  />
                        <label for="US">US</label>
                    </div>
                    
                </div>
                <x-adminlte-textarea label="Search Catalog" type="text-area" class="Asins" name="catalog_asins" placeholder="Enter Asin" rows="4" />
                <b><p class="text-danger" id="error"></p></b>
                <x-adminlte-button label="Search" type="submit" theme="primary" icon="fas fa-search text-danger" class="search-catalog btn-sm float-right mt-2 " />
            </div>
        </div>
    </div>
    <div class="col-2"></div>
</div>
<div class="row d-none display-data">
    <table class="table table-bordered yajra-datatable table-striped text-center table-sm ">
        <thead class="">
            <tr class="bg-info thead"></tr>
            <!-- <tr class="bg-info">
                <th>Seller Id</th>
                <th>ASIN</th>
                <th>Source</th>
                <th>Height</th>
                <th>Width</th>
                <th>Length</th>
                <th>Unit</th>
                <th>Weight</th>
                <th>Weight Unit</th>
                <th>Brand</th>
                <th>Manufacturer</th>
                <th>Price</th>
            </tr> -->
        </thead>
        <tbody class="search-data">
        </tbody>
    </table>
</div>
@stop

@section('js')
<script type="text/javascript">
    
    $('.search-catalog').on('click', function(){
        
        $('.display-data').addClass('d-block');
        let catalog_asins = $('.Asins').val();
        let source = $('input[name="source"]:checked').val();

        if(!$('input[name="source"]:checked').val() ){
            alert('Please choose source');
            return false;
        }
        else if(catalog_asins == ''){
            document.getElementById('error').innerHTML = 'Please enter Asin *';
            $('.thead').empty();
            $('.search-data').empty();

            return false;
        }else{
            document.getElementById('error').innerHTML = '';
            $.ajax({
                method: 'post',
                url: "{{ route('catalog.asin.search') }}",
                data: {
                    "catalog_asins" : catalog_asins,
                    "source": source,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(result) {
                    let data = '';
                    let head = "";
                    let str_replace = '';
                    $('.thead').empty();
                    
                    $.each(result[0], function(key, record){
                        head += " <td>"+ key +"</td> ";
                        str_replace = head.replace(/_+/g, ' ').toUpperCase() ;
                    });
                    $('.thead').append(str_replace);

                    $.each(result, function(key1, record1){
                        data += '<tr>';
                        $.each(record1, function(key2, value){
                            data += "<td>"+ value +"</td>"
                        });
                        data +='</tr>';
                    });
                    $('.search-data').html(data);
                },
                error: function(result) {
                    // alert('Data not found!');
                }
            });
        }
    });
    
    $(document).on('click', ".delete", function(e) {
        e.preventDefault();
        let bool = confirm('Are you sure you want to push this asin to Bin?');

        if (!bool) {
            return false;
        }
        let self = $(this);
        let id = self.attr('data-id');
        self.prop('disable', true);
        $.ajax({
            method: 'post',
            url: '/catalog/remove/asin/' + id,
            data: {
                "_token": "{{ csrf_token() }}",
            },
            response: 'json',
            success: function(response) {
                alert('Delete successfully');
                yajra_datatable();
            },
        });

    });

    $(document).on('click', '.truncate', function(){
        let bool = confirm('Are you sure you want to truncate this selected table?');
        if (!bool) {
            return false;
        }
    });

</script>
@stop
