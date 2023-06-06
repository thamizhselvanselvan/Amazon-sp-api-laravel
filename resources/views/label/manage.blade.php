@extends('adminlte::page')
@section('title', 'Label')

@section('css')


    <link rel="stylesheet" href="/css/styles.css">
    <style>
        .table td {
            padding: 0;
            padding-left: 5px;
        }

        .btn-group-sm .btn,
        .btn-sm {
            padding-left: 0.2rem 0.2rem;
        }
    </style>
@stop

@section('content_header')
    <div class="row">
        <h1 class="m-0 text-dark col-3">Label Management</h1>
    </div>

    <div class="row d-flex">
        <div class="col-4 text-left">
            <div class="d-inline-block">
                <a href="{{ route('custom.label.index') }}">
                    <x-adminlte-button label="Custom Label" theme="primary" icon="fas fa-file-upload" class="btn-sm" />
                </a>
            </div>
        </div>
        <div class="col-8 text-right ">
            <div class="dropdown d-inline-block">
                <button class="btn btn-primary dropdown-toggle btn-sm" type="button" id="dropdownMenuButton"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Search
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <a class="dropdown-item" href="search-label">Search By Bag No.</a>
                    <a class="dropdown-item" href="{{ route('lable.search.amazon-order-id') }}">Search By Order Id / AWB
                        No.</a>
                    <a class="dropdown-item" href="{{ route('lable.search.date') }}">Search By Date</a>
                </div>
            </div>

            <div class="d-inline-block">
                <a href="{{ route('upload.label.page') }}">
                    <x-adminlte-button label="Add New Records" theme="primary" icon="fas fa-file-upload" class="btn-sm" />
                </a>
            </div>
            <div class="d-inline-block">
                <a href="{{ route('label.missing.order') }}">
                    <x-adminlte-button label="Fetch Missing Orders" theme="primary" icon="fas fa-sync" class="btn-sm" />
                </a>
            </div>
            <div class="d-inline-block">
                <a href="{{ route('label.missing.address') }}">
                    <x-adminlte-button label="Upload Missing Address" theme="primary" icon="fas fa-file-upload"
                        class="btn-sm" />
                </a>
            </div>
            <div class="d-inline-block">
                <a href="{{ route('orders.csv.import') }}">
                    <x-adminlte-button label="Order Import" theme="primary" icon="fas fa-file-upload" type="button"
                        class="btn-sm" />
                </a>
            </div>
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
    <script>
        $(document).ready(function() {
            $('.check_all').change(function() {

                if ($('.check_all').is(':checked')) {
                    $('.check_options').prop('checked', true);
                } else {
                    $('.check_options').prop('checked', false);
                }
            });

            $('#print_selected').click(function() {
                // alert('working');
                let id = '';
                let count = '';
                $("input[name='options[]']:checked").each(function() {
                    if (count == 0) {
                        id += $(this).val();
                    } else {
                        id += '-' + $(this).val();
                    }
                    count++;
                    window.location.href = '/label/print-selected/' + id;
                });
                // alert(id);
            });

            $('#download_selected').click(function() {
                let id = '';
                let count = '';
                let arr = '';
                $("input[name='options[]']:checked").each(function() {
                    if (count == 0) {
                        id += $(this).val();
                    } else {
                        id += '-' + $(this).val();
                    }
                    count++;
                });
                $.ajax({
                    method: 'POST',
                    // url: "{{ url('/label/select-download') }}",
                    url: "{{ route('label.download.selected') }}",
                    data: {
                        'id': id,
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(response) {
                        arr += response;
                        window.location.href = '/label/zip-download/' + arr;
                        // alert('Export pdf successfully');
                    }
                });
            });
        });
    </script>

@stop
