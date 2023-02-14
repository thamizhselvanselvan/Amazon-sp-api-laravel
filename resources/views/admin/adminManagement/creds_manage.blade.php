@extends('adminlte::page')

@section('title', 'Credentials Management')

@section('css')

<link rel="stylesheet" href="/css/styles.css">
@stop

@section('content_header')
<div class="col">
    <h1 class="m-0 text-dark text-left">Credentials Management</h1>
</div>
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

@section('content')
<div class="row">
    <div class="col-2">
        <div id="region">
            <x-adminlte-select name="Region" id="Region_input" label="Select Region:">
                <option value="0">Select Region </option>
                @foreach ($data_mws as $list)
                <option value="{{ $list->id }}" {{ $request_Region == $list->id ? 'selected' : '' }}>{{$list->region }}</option>
                @endforeach
            </x-adminlte-select>
        </div>
    </div>
</div>

<div class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">

        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Credentials Priority</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="font-size:15px">
                <form action="{{ route('save.creds.priority') }}">
                    <h5>Select Priority</h5>
                    <input type="hidden" class="temp" name="sell_id" value="">
                    <div class="row border">
                        <div class="col-2">
                            <label for="P1">P1</label>
                            <input type="radio" class="priority" name="priority" value="1">
                        </div>
                        <div class="col-2">
                            <label for="P1">P2</label>
                            <input type="radio" class="priority" name="priority" value="2">
                        </div>
                        <div class="col-2">
                            <label for="P3">P3</label>
                            <input type="radio" class="priority" name="priority" value="3">
                        </div>
                        <div class="col-2 usap4 d-none">
                            <label for="P4">P4</label>
                            <input type="radio" class="priority" name="priority" value="4">
                        </div>
                    </div>

                    <div class="col-12 float-left mt-2">
                        <x-adminlte-button label="Update" theme="success" class="btn btn-sm " icon="fas fa-save  " type="submit" id="catalog_export" />
                        <button type="button" class="btn-sm btn btn-danger" data-dismiss="modal"><i class='fas fa-window-close'></i> Close</button>
                    </div>
                </form>
            </div>
        </div>


    </div>
</div>
<table class="table table-bordered yajra-datatable table-striped " id="creds_table">
    <thead>
        <tr class="table-info">
            <th> DB ID</th>
            <th>Store Name</th>
            <th>Merchant ID</th>
            <th>Credentials In use</th>
            <th>priority</th>
            <th>Update</th>
        </tr>
    </thead>
    <tbody id="report_table_body">
    </tbody>
</table>
@stop


@section('js')
<script type="text/javascript">
    $.extend($.fn.dataTable.defaults, {
        pageLength: 100,
    });
    let yajra_table = $('.yajra-datatable').DataTable({
        processing: true,
        serverSide: true,
        stateSave: true,
        ajax: {
            url: "{{ $url }}",
            type: 'get',
            headers: {
                'content-type': 'application/x-www-form-urlencoded',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: function(d) {
                d.region = $('#Region_input').val();
            },
        },
        columns: [{
                data: 'id',
                name: 'id',
                orderable: false,
                searchable: false
            },
            {
                data: 'store_name',
                name: 'store_name',
                orderable: false,
                searchable: false
            },
            {
                data: 'merchant_id',
                name: 'merchant_id',
                orderable: false,
                searchable: false
            },
            {
                data: 'credential_use',
                name: 'credential_use',
                orderable: false,
                searchable: false
            },
            {
                data: 'Creds_priority',
                name: 'Creds_priority',
                orderable: false,
                searchable: false
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            },

        ]
    });

    $('#region').on('change', function() {
        let region = $('#Region_input').val();
        if (region == '0') {
            alert('Region is Requirerd');
            return false;
        }
        window.location = "/admin/creds/manage/" + region;

        yajra_table.redraw();

    });

    $(document).on('click', '.creds', function() {
        let val = $(this).attr('value');
        let data = val.split("_");
        let region_id = (data['0']);
        let id = (data['1']);
        if (region_id == '4') {

            $(".usap4").removeClass("d-none")
        }
        $(".temp").val(id);
    });

    // $("#catalog_export").on("click", function() {
    //     let newval = $(".temp").val();
    //     alert(newval);

    //     return false;
    // });
</script>
@stop