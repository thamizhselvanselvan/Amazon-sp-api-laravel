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

            <!-- <a href="{{route('catalog.asin.dastination.export')}}">
                <x-adminlte-button label="Asin Export" theme="primary" icon="fas fa-file-export" class="btn-sm" />
            </a>

            <x-adminlte-button label="Download Asin" theme="primary" icon="fas fa-file-download" data-toggle="modal"
                data-target="#exampleModal" class="btn-sm"></x-adminlte-button> -->

            <a href="{{ route('catalog.destination.download.template') }}">
                <x-adminlte-button label="Download Template" theme="primary" icon="fas fa-file-download"
                    id="exportUniversalTextiles" class="btn-sm" />
            </a>

    
            <x-adminlte-button label="Asin Truncate" theme="primary" icon="fas fa-trash text-danger"
                 class="btn-sm" data-toggle="modal" data-target="#destinationTruncate"></x-adminlte-button>
            
            <div class="modal fade" id="destinationTruncate" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
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
                            <form action="{{ route('catalog.asin.destination.truncate') }}">
                                <h5>Select Destination</h5>
                                <div class="row ">
                                    <div class="col-2">
                                        <label for="AE">AE</label>
                                        <input type="checkbox" name="destination[]" value="AE" >
                                    </div>
                                    <div class="col-2">
                                        <label for="IN">IN</label>
                                        <input type="checkbox" name="destination[]" value="IN" >
                                    </div>
                                    <div class="col-2">
                                        <label for="US">US</label>
                                        <input type="checkbox" name="destination[]" value="US" >
                                    </div>
                                </div><br>

                                <h5>Select Priority</h5>
                                <div class="row ">
                                    <div class="col-2">
                                        <label for="P1">P1</label>
                                        <input type="radio" class="destination-priority" name="priority" value="1"  >
                                    </div>
                                    <div class="col-2">
                                        <label for="P2">P2</label>
                                        <input type="radio" class="destination-priority" name="priority" value="2"  >
                                    </div>
                                    <div class="col-2 ">
                                        <label for="P3">P3</label>
                                        <input type="radio" class="destination-priority" name="priority" value="3"  >
                                    </div>
                                </div>
                                <div class="col-12 float-left mt-2">
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

            <!-- <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Asin Download</h5>
                            <button type="button" class="close btn-sm" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">

                            <a href="{{ route('catalog.download.asin.destination') }}">
                                <h6>Download ASIN </h6>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div> -->
            </a>
            <!-- <a href="{{ route('catalog.asin.destination.bin') }}">
                <x-adminlte-button label="Bin" theme="primary" icon="fas fa-trash" class="btn-sm" />
            </a> -->
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

        <!-- <table class="table table-bordered yajra-datatable table-striped text-center table-sm">
            <thead>
                <tr class="bg-info">
                    <th>S/N</th>
                    <th>ASIN</th>
                    <th>Destination</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table> -->

    </div>
</div>
@stop

@section('js')
<script type="text/javascript">

$(function() {
    yajra_datatable();
});

function yajra_datatable() {

    // let yajra_table = $('.yajra-datatable').DataTable({
    //     processing: true,
    //     serverSide: true,
    //     destroy: true,
    //     pageLength: 200,
    //     ajax: "{{ url('catalog/asin-destination') }}",
    //     columns: [{
    //             data: 'id',
    //             name: 'id',
    //             orderable: false,
    //             searchable: false
    //         },
    //         {
    //             data: 'asin',
    //             name: 'asin',
    //             orderable: false
    //         },
    //         {
    //             data: 'destination',
    //             name: 'destination',
    //             orderable: false
    //         },
    //         {
    //             data: 'action',
    //             name: 'action',
    //             orderable: false,
    //             searchable: false
    //         },

    //     ]
    // });
}
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

// $(document).on('click', ".delete", function(e) {
//     e.preventDefault();
//     let bool = confirm('Are you sure you want to push this asin to Bin?');

//     if (!bool) {
//         return false;
//     }
//     let self = $(this);
//     let id = self.attr('data-id');

//     self.prop('disable', true);

//     $.ajax({
//         method: 'post',
//         url: '/catalog/remove/asin/' + id,
//         data: {
//             "_token": "{{ csrf_token() }}",
//         },
//         response: 'json',
//         success: function(response) {
//             alert('Delete successfully');
//             // location.reload()
//             yajra_datatable();
//         },
//     });

// });

</script>
@stop
