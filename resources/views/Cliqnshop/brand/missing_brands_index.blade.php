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
@section('title', 'Cliqnshop Missing Brands')


@section('content_header')
    <div class="row">
        <h3>Product List Without Brand</h3>
    </div>

@stop

@section('content')

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
                <th>Asin</th>
                <th>Product Name</th>
            </tr>
        </thead>
        <tbody>

        </tbody>
    </table>

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
                        data: 'asin',
                        name: 'asin'
                    },
                    {
                        data: 'label',
                        name: 'label'
                    },
                ]
            });
        });
        $('#site_id').change(function() {
            let site_id = $('#site_id').val();
            window.location = "/cliqnshop/brand/missing/" + site_id
        });
    </script>
@stop
