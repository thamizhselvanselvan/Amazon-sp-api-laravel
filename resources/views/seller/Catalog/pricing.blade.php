@extends('adminlte::page')
@section('title', 'Import')

@section('content_header')
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
<div class="row">
    <h1 class="m-0 text-dark">ASIN Price Details</h1>
    <div class="col text-right">
        <a href="/seller/price/get">
            <x-adminlte-button label="Get Price" theme="primary" icon="fas fa-sync" id='pricing' />
        </a>
        <a href="/seller/price/export">
            <x-adminlte-button label="Pricing CSV Export" theme="primary" icon="fas fa-file-import" id='pricing' />
        </a>
        <x-adminlte-button label="Download Price CSV" theme="primary" icon="fas fa-file-download" class="file_download_modal_btn" />
    </div>
</div>
@stop

@section('content')
@csrf
<div class="modal fade" id="file_download_modal" tabindex="-1" role="dialog" aria-labelledby="FileDownloadModal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Download Asin Details CSV</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="file_download_display">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id='file_download_modal_close'>Close</button>
            </div>
        </div>
    </div>
</div>

<table class="table table-bordered yajra-datatable table-striped">
    <thead>
        <tr class="length">
            <th>S/N</th>
            <th>ASIN</th>
            <th>Is Fulfilment By Amazon</th>
            <th>Price</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>

@stop

@section('js')
<script type="text/javascript">
    let yajra_table = $('.yajra-datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ url('seller/price/details') }}",
        columns: [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false
            },
            {
                data: 'asin',
                name: 'asin',
                orderable: false
            },
            {
                data: 'is_fulfilment_by_amazon',
                name: 'is_fulfilment_by_amazon',
                orderable: false
            },
            {
                data: 'price',
                name: 'price',
                orderable: false,
                searchable: false
            },

        ]
    });

    $(".file_download_modal_btn").on('click', function(e) {

        // alert('success');
        fileDownloadModal();
    });

    function fileDownloadModal() {

        let self = $(this);
        let file_display = $('.file_download_display');
        let file_modal = $("#file_download_modal");

        $.ajax({
            url: "/seller/price/download",
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.error) {
                    alert('Error');
                }
                if (response.success) {
                    file_modal.modal('show');
                    let html = '<ul>';
                    if (response.files_lists == '') {

                        html += "<span class ='p-0 m-0'>File Is Downloading, Please Wait... </span>";
                    } else {

                        $.each(response.files_lists, function(index, value) {

                            let file_name = Object.keys(value)[0];
                            let file_time = value[file_name];

                            html += "<li class='p-0 m-0'>";
                            html += "<a href='/seller/price/download/" + file_name + "' class='p-0 m-0'> Part " + parseInt(index + 1) + "</a> ";
                            html += file_time;
                            html += "</li>";

                        });
                        html += '</ul>';
                    }
                    file_display.html(html);
                }
            }
        });
    }

    $("#file_download_modal_close").on('click', function() {
        $('#file_download_modal').modal('hide');
    });
</script>
@stop