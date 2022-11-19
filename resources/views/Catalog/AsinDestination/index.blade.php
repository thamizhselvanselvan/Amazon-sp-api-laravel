@extends('adminlte::page')

@section('title', 'ASIN Destination')

@section('css')
<link rel="stylesheet" href="/css/styles.css">
@stop

@section('content_header')
<div class="row">
    <div class="col">

        <h1 class="m-0 text-dark"> ASIN Destination </h1>
    </div>
    <div class="col ">

        <h2 class="mb-4 float-right">

            <a href="import-asin-destination">
                <x-adminlte-button label="Asin Bulk Import" theme="primary" icon="fas fa-file-import" class="btn-sm" />
            </a>

            <a href="{{ route('catalog.destination.download.template') }}">
                <x-adminlte-button label="Download Template" theme="primary" icon="fas fa-file-download" id="exportUniversalTextiles" class="btn-sm" />
            </a>

            <!-- <x-adminlte-button label="Asin Truncate" theme="primary" icon="fas fa-trash text-danger" class="btn-sm"
                    data-toggle="modal" data-target="#destinationTruncate"></x-adminlte-button> -->

            <div class="modal fade" id="destinationTruncate" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">ASIN Table Truncate</h5>
                            <button type="button" class="close btn-sm" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body" style="font-size:15px">
                            <form action="{{ route('catalog.asin.destination.truncate') }}">
                                <h5>Select Destination</h5>
                                <div class="row ">
                                    {{-- <div class="col-2">
                                            <label for="AE">AE</label>
                                            <input type="checkbox" name="destination[]" value="AE">
                                        </div> --}}
                                    <div class="col-2">
                                        <label for="IN">IN</label>
                                        <input type="checkbox" name="destination[]" value="IN">
                                    </div>
                                    <div class="col-2">
                                        <label for="US">US</label>
                                        <input type="checkbox" name="destination[]" value="US">
                                    </div>
                                </div><br>

                                <h5>Select Priority</h5>
                                <div class="row ">
                                    <div class="col-2">
                                        <label for="P1">P1</label>
                                        <input type="radio" class="destination-priority" name="priority" value="1">
                                    </div>
                                    <div class="col-2">
                                        <label for="P2">P2</label>
                                        <input type="radio" class="destination-priority" name="priority" value="2">
                                    </div>
                                    <div class="col-2 ">
                                        <label for="P3">P3</label>
                                        <input type="radio" class="destination-priority" name="priority" value="3">
                                    </div>
                                </div>
                                <div class="col-12 float-left mt-2">
                                    <x-adminlte-button label="Truncate" theme="danger" class="btn btn-sm truncate" icon="fas fa-trash " type="submit" />
                                </div>
                            </form>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-danger" data-dismiss="modal">Close</button>
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
        <form action=" {{ route('catalog.asin.destination.search.delete') }} " method="POST">
            @csrf

            <div class="card ">
                <div class="card-header text-center mt-0 pt-0 mb-0 pb-0">
                    <h3>Search ASIN And Delete</h3>
                </div>
                <div class="card-body ">
                    <label for="Select Source" class="mt-0">Select Source</label><br>
                    <div class="row ">
                        <div class="col-1">
                            <input type="radio" class="Asin-source" name="source" value="IN" />
                            <label for="IN">IN</label>
                        </div>
                        <div class="col-1">
                            <input type="radio" class="Asin-source" name="source" value="US" />
                            <label for="US">US</label>
                        </div>

                    </div>
                    <label for="Select Source" class="mt-0">Select Priority</label><br>
                    <div class="row ">
                        <div class="col-1">
                            <input type="radio" class="destination-priority" name="priority" value="1">
                            <label for="P1">P1</label>
                        </div>
                        <div class="col-1">
                            <input type="radio" class="destination-priority" name="priority" value="2">
                            <label for="P2">P2</label>
                        </div>
                        <div class="col-1 ">
                            <input type="radio" class="destination-priority" name="priority" value="3">
                            <label for="P3">P3</label>
                        </div>
                    </div>
                    <x-adminlte-textarea label="Enter ASIN (max:10000)" type="text-area" class="Asins" name="Asins" placeholder="Enter Asin" rows="6" />
                    <b>
                        <p class="text-danger" id="error"></p>
                    </b>
                    <x-adminlte-button label="Search & Delete" type="submit" theme="primary" icon="fas fa-trash text-danger" class="search&delete btn-sm float-right mt-2 " onclick="return confirm('Are you sure you want to delete these asins?')" />
                </div>
            </div>
        </form>
    </div>
    <div class="col-2"></div>
</div>
@stop

@section('js')
<script type="text/javascript">
    $(document).on('click', '.trash', function() {
        let bool = confirm('Are you sure you want to delete?');
        if (!bool) {
            return false;
        }

    });

    $(document).on('click', '.truncate', function() {

        let bool = confirm('Are you sure you want to truncate this selected table ?');
        if (!bool) {
            return false;
        }

    });
</script>
@stop