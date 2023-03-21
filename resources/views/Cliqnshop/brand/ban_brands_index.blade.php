@extends('adminlte::page')

@section('css')

    <link rel="stylesheet" href="/css/styles.css">
    <style>
        .table td {
            padding: 0;
            padding-left: 5px;
        }

        .table th {
            padding: 2;
            padding-left: 5px;
        }
    </style>
@stop
@section('title', 'Cliqnshop Banned Brands')


@section('content_header')
    <div class="row">
        <h3>Banned Brands</h3>
    </div>

@stop

@section('content')
    <div class="alert_display">
        @if (request('success'))
            <div class="alert alert-success alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ request('success') }}</strong>
            </div>
        @endif
    </div>
    <div class="row">
        <div class="col">
            <h6 class="mb-4">
                <x-adminlte-button class="ml-2" label="Add Brand" theme="primary" icon="fas fa-plus" data-toggle="modal"
                    data-target="#exampleModal" />
                <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Add New Brand</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="SubmitForm">
                                    <div class="form-group w-50">
                                        <x-adminlte-select name="site" id="site" label="Site:">
                                            <option value="" selected>Select Site</option>
                                            @foreach ($sites as $site)
                                                @if ($site->code == 'in')
                                                    {{ $site->code = 'India' }}
                                                @elseif ($site->code == 'uae')
                                                    {{ $site->code = 'UAE' }}
                                                @endif
                                                <option value="{{ $site->siteid }}">{{ $site->code }}</option>
                                            @endforeach
                                        </x-adminlte-select>
                                        {{-- <span class="text-danger" id="siteErrorMsg"></span> --}}
                                    </div>
                                    <div class="form-group">
                                        <label for="brand" class="col-form-label">Brand:</label>
                                        <div class="w-50">
                                            <x-adminlte-input name="brand" id="brand"></x-adminlte-input>
                                            {{-- <span class="text-danger" id="keywordErrorMsg"></span> --}}
                                        </div>
                                    </div>
                                    <div class="col-form">

                                        {{-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> --}}
                                        <x-adminlte-button class="mb-2" label="Submit" theme="primary" icon="fas fa-plus"
                                            type="submit" />

                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </h6>


            <div class="modal fade" id="EditModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Update Brand</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="SubmitForm_edit">
                                <div class="form-group w-50">
                                    <x-adminlte-select name="site" id="editsite" label="Site:">
                                        <option value="" selected>Select Site</option>
                                        @foreach ($sites as $site)
                                            @if ($site->code == 'in')
                                                {{ $site->code = 'India' }}
                                            @elseif ($site->code == 'uae')
                                                {{ $site->code = 'UAE' }}
                                            @endif
                                            <option value="{{ $site->siteid }}">{{ $site->code }}</option>
                                        @endforeach
                                    </x-adminlte-select>
                                    {{-- <span class="text-danger" id="siteErrorMsg"></span> --}}
                                </div>
                                <div class="form-group">
                                    <label for="brand" class="col-form-label">Brand:</label>
                                    <div class="w-50">
                                        <x-adminlte-input name="brand" id="editbrand"></x-adminlte-input>
                                        {{-- <span class="text-danger" id="keywordErrorMsg"></span> --}}
                                    </div>
                                </div>
                                <div class="col-form">

                                    {{-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> --}}
                                    <x-adminlte-button class="mb-2" label="Submit" theme="primary" icon="fas fa-plus"
                                        type="submit" />

                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>

            <table class="table table-bordered data-table">
                <div class="form-group" style="width: 200px">
                    <x-adminlte-select name="site_id" id="site_id">
                        <option value='' selected>Select Site</option>
                        @foreach ($sites as $site)
                            @if ($site->code == 'in')
                                {{ $site->code = 'India' }}
                            @elseif ($site->code == 'uae')
                                {{ $site->code = 'UAE' }}
                            @endif
                            <option value="{{ $site->siteid }}">{{ $site->code }}</option>
                        @endforeach
                    </x-adminlte-select>
                </div>
                <thead>
                    <tr class="table-info">
                        <th>ID</th>
                        <th>Brand</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>

@stop

@section('js')

    <script type="text/javascript">
        $.extend($.fn.dataTable.defaults, {
            pageLength: 50,
        });

        $(function() {

            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ url($url) }}",
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'brand',
                        name: 'brand'
                    },
                    {
                        data: 'action',
                        name: 'action'
                    },
                ]
            });
        });
        $(document).on('click', '#delete', function() {
            let bool = confirm('Are you sure you want to delete this ?');
            if (!bool) {
                return false;
            }
            let id = $(this).attr('value');
            $.ajax({
                url: "/cliqnshop/brand/ban/delete/"+id,
                type: "POST",
                data: {
                    "_token": "{{ csrf_token() }}",
                    id: id,
                },
                success: function(response) {
                    $('#successMsg').show();
                    console.log(response);
                    if (response == 'Successfully') {
                        window.location.href =
                            '/cliqnshop/brand/ban?success=brand Deleted Successfully';
                    }
                },
        });
    });

        $(document).on('click', '#edit', function() {
            $('#EditModal').modal('show');
             let id = $(this).attr('value');
            let site_id = $(this).attr("data-siteid");
            // let site_name = $(this).attr("data-site_name");
            let brand = $(this).attr("data-brand");
            $("#editbrand").val(brand);

            var trends = document.getElementById('editsite'), trend, i;
            for (i = 0; i < trends.length; i++) {
                trend = trends[i];
                if (trend.value == site_id)
                {
                    trend.setAttribute('selected', true);
                }
            }

            $('#SubmitForm_edit').on('submit', function(e) {
            e.preventDefault();
            let site = $('#editsite').val();
            let brand = $('#editbrand').val();
            $.ajax({
                url: "/cliqnshop/brand/ban/edit/"+id,
                type: "POST",
                data: {
                    "_token": "{{ csrf_token() }}",
                    id: id,
                    site: site,
                    brand: brand,
                },
                success: function(response) {
                    $('#successMsg').show();
                    console.log(response);
                    if (response == 'Successfully') {
                        window.location.href =
                            '/cliqnshop/brand/ban?success=brand Updated Successfully';
                    }
                },
                error: function(response) {
                    // $('#siteErrorMsg').text(response.responseJSON.errors.site);
                    if (typeof response.responseJSON.errors.site !== 'undefined') {
                        alert(response.responseJSON.errors.site);
                    }
                    // $('#keywordErrorMsg').text(response.responseJSON.errors.keyword);
                    if (typeof response.responseJSON.errors.brand !== 'undefined') {
                        alert(response.responseJSON.errors.brand);
                    }
                },
            });
        });
        });

        $('.close').click(function() {
        $('#EditModal').modal('hide');
    });


        $('#site_id').change(function() {
            let site_id = $('#site_id').val();
            window.location = "/cliqnshop/brand/ban/" + site_id
        });
        $('#SubmitForm').on('submit', function(e) {
            e.preventDefault();
            let site = $('#site').val();
            let brand = $('#brand').val();
            $.ajax({
                url: "/cliqnshop/brand/ban",
                type: "POST",
                data: {
                    "_token": "{{ csrf_token() }}",
                    site: site,
                    brand: brand,
                },
                success: function(response) {
                    $('#successMsg').show();
                    console.log(response);
                    if (response == 'Successfully') {
                        window.location.href =
                            '/cliqnshop/brand/ban?success=Keyword Added Successfully';
                    }
                },
                error: function(response) {
                    // $('#siteErrorMsg').text(response.responseJSON.errors.site);
                    if (typeof response.responseJSON.errors.site !== 'undefined') {
                        alert(response.responseJSON.errors.site);
                    }
                    // $('#keywordErrorMsg').text(response.responseJSON.errors.keyword);
                    if (typeof response.responseJSON.errors.brand !== 'undefined') {
                        alert(response.responseJSON.errors.brand);
                    }
                },
            });
        });
    </script>
@stop
