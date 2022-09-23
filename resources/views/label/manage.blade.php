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

<div class="row">
    <h2 class="mb-4 text-right col">
        <a href="search-label">
            <x-adminlte-button label="Search Label" theme="primary" icon="fas fa-search" class="btn-sm" />
        </a>
        
        <a href="upload">
            <x-adminlte-button label="Add New Records" theme="primary" icon="fas fa-file-upload" class="btn-sm" />
        </a>
        <a href="missing">
            <x-adminlte-button label="Fetch Missing Orders" theme="primary" icon="fas fa-sync" class="btn-sm" />
        </a>
        
        <a href="missing/address">
            <x-adminlte-button label="Upload Missing Address" theme="primary" icon="fas fa-file-upload" class="btn-sm" />
        </a>
    </h2>
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
                url: "{{ url('/label/select-download') }}",
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