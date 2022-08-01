@extends('adminlte::page')
@section('title', 'Import')

@section('content_header')
<h1 class="m-0 text-dark">Amazon Data</h1>
@stop

@section('content')
@csrf

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
        <div class="row">

            <div class="col d-flex">
                <h2 class="mb-4">
                    <a href="{{ route('catalog.amazon.product') }}">
                        <x-adminlte-button label="Fetch Catalog From Amazon" theme="primary" icon="fas fa-file-export"
                            id="exportUniversalTextiles" />
                    </a>
                </h2>
            </div>
            <div class="col d-flex justify-content-end">

                <x-adminlte-select label="Select Country" name="country" id="country" class="float-right">
                    <option value="NULL">select country</option>
                    @foreach ($sources as $source)
                    <option value="{{$source->source}}">{{$source->source}}</option>
                    @endforeach
                </x-adminlte-select>

            </div>
        </div>
        <table class="table table-bordered yajra-datatable table-striped">
            <thead>
                <tr>
                    <th>S/N</th>
                    <th>Asin</th>
                    <th>Source</th>
                    <th>Name</th>
                    <th>Dimension</th>
                    <th>Weight</th>
                    <th>Price</th>

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
$('#country').on('change', function() {
    let country_code = $(this).val();
    yajraTable(country_code);
    // alert(country_code);
});

function yajraTable(country_code) {

    let yajra_table = $('.yajra-datatable').DataTable({
        processing: true,
        serverSide: true,
        destroy: true,
        ajax: {
            url: "{{ url('catalog/product') }}",
            data: {
                "country_code": country_code,
                "_token": "{{ csrf_token() }}",
            },
        },
        pageLength: 200,
        lengthMenu: [50, 100, 200, 500],
        columns: [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false
            },
            {
                data: 'asin',
                name: 'asin'
            },
            {
                data: 'source',
                name: 'source'
            },
            {
                data: 'title',
                name: 'title'
            },
            {
                data: 'item_dimensions',
                name: 'item_dimensions'
            },
            {
                data: 'weight',
                name: 'weight'
            },
            {
                data: 'amount',
                name: 'amount'
            },
        ]
    });

}
</script>
@stop
