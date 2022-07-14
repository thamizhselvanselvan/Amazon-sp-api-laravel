@extends('adminlte::page')

@section('title', 'ShipNTrack')

@section('content_header')
<div class="row">
    <h1 class="m-0 text-dark col">ShipNTrack Management</h1>
    <h2 class="mb-4 text-right col">
        <!-- <a href="search-invoice">
            <x-adminlte-button label="Search Invoice" theme="primary" icon="fas fa-search" class="btn-sm" />
        </a> -->

    </h2>
</div>
@stop

@section('content')
<div class="row">
    <div class="col"></div>
    <div class="col">
        <div class="row justify-content-end">

            <x-adminlte-select label="Source-Destination:" theme="primary" name="source-destinaion" id="source">
                <option value="">Select Source-Destination</option>
                @foreach ($sourcedestination as $value)
                <option value="{{$value->source_destination}}">{{$value->source_destination}}</option>
                @endforeach
            </x-adminlte-select>
            <div class="form-group ml-2 shipRow">

                <a href=" upload">
                    <x-adminlte-button label="Add New Records" theme="primary" icon="fas fa-file-upload"
                        class="btn-sm" />
                </a>
                <a href="template/download">
                    <x-adminlte-button label="Download Template" theme="primary" icon="fas fa-download"
                        class="btn-sm" />
                </a>
            </div>
        </div>
    </div>
</div>
<div class="pl-2 d-none">
    <table class="table table-bordered table-striped text-center table-sm">
        <thead>
            <tr class="text-bold bg-info">
                <th>Sr</th>
                <th>Weight</th>
                <th>Base Rate</th>
                <th>Commission</th>
                <th>Source-Destination</th>
            </tr>
        </thead>
        <tbody id="dataTable">
        </tbody>

    </table>
</div>
@stop

@section('js')
<script>
// let yajra_table = $('.yajra-datatable').DataTable({

//         processing: true,
//         serverSide: true,
//         ajax: "{{ url('/shipntrack/get') }}",
//         pageLength: 50,
//         // searching: false,
//         columns: [{
//                 data: 'DT_RowIndex',
//                 name: 'DT_RowIndex',
//                 orderable: false,
//                 searchable: false
//             },
//             {
//                 data: 'weight',
//                 name: 'weight',
//             },
//             {
//                 data: 'base_rate',
//                 name: 'base_rate',
//             },
//             {
//                 data: 'commission',
//                 name: 'commission',
//             },
//             {
//                 data: 'source_destination',
//                 name: 'source_destination',
//             },

//         ],
//     }

// );

$(document).ready(function() {

    $('#source').on('change', function() {

        $('.d-none').removeClass();
        let option = $(this).val();

        $.ajax({
            method: 'GET',
            url: "{{ url('/shipntrack/get')}}",
            data: {
                'option': option,
                "_token": "{{ csrf_token() }}",
            },
            success: function(response) {
                console.log(response);
                let body = '';
                $.each(response, function(i, response) {
                    body += "<tr><td>" + response.id + "</td><td>" + response
                        .weight + "</td><td>" + response.base_rate + "</td><td>" +
                        response.commission + "</td><td>" + response
                        .source_destination + "</td></tr>"
                });
                $('#dataTable').html(body);
            },
        });
    });
});
</script>
@stop
