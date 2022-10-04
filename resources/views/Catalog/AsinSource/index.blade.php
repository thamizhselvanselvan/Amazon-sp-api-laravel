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
@stop

@section('js')
<script type="text/javascript">
    
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
