@extends('adminlte::page')
@section('title', 'OMS Status Master')

@section('css')
    <link rel="stylesheet" href="/css/styles.css">
@stop

@section('content_header')
    <div class='row'>

        <div class="col text-center">
            <h1 class="mb-1 text-dark font-weight-bold"> OMS Status Master </h1>
        </div>
    </div>

    <form action="{{ isset($records) ? route('update.oms.status', $records[0]->id) : route('add.oms.status') }}"
        method="POST">
        @csrf
        <div class="card ">
            <div class="card-body">
                <div class="row">
                    @if (isset($records))
                        @foreach ($records as $record)
                        <div class="col">
                                <x-adminlte-input label="Code" type="text" name="code" placeholder="code"
                                    value="{{ $record->code }}" />
                            </div>
                            <div class="col">
                                <x-adminlte-input label="Status" type="text" name="status" placeholder="status"
                                    value="{{ $record->status }}" />
                            </div>
                            <div class="col">
                                <x-adminlte-input label="active" type="text" name="active" placeholder="active"
                                    value="{{ $record->active }}" />
                            </div>
                        @endforeach
                    @else
                    <div class="col">
                            <x-adminlte-input label="Code" type="text" name="code" placeholder="code" />
                        </div>
                        <div class="col">
                            <x-adminlte-input label="Status" type="text" name="status" placeholder="status" />
                        </div>
                        <div class="col">
                            <x-adminlte-input label="active" type="text" name="active" placeholder="active" />
                        </div>
                    @endif
                </div>

                <a href="{{ route('oms.home') }}"
                    class="{{ isset($records) ? 'btn btn-primary btn-sm' : 'd-none' }}">
                    <i class="fas fa-arrow-left"></i> back
                </a>

                <a href="{{ route('recycle.oms.status') }}" class="btn btn-primary btn-sm float-right ml-2">
                    <i class="far fa-trash-alt text-danger"></i> Bin
                </a>
                <x-adminlte-button label="{{ isset($records) ? 'Update Status' : 'Add  status' }}"
                    type="submit" theme="{{ isset($records) ? 'success' : 'primary' }}"
                    icon=" {{ isset($records) ? 'fas fa-edit' : 'fa fa-plus' }}" class="btn-sm float-right" />

            </div>
        </div>
    </form>

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
                @elseif($message = Session::get('danger'))
                    <div class="alert alert-danger alert-block">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        <strong>{{ $message }}</strong>
                    </div>
                @endif
            </div>

            <table class="table table-bordered yajra-datatable table-striped text-center table-sm">
                <thead class='table-primary'>
                    <tr class="length">
                        <th>S/N</th>
                        <th>Code</th>
                        <th>Status</th>
                        <th>Active</th>
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
    <script>
        let yajra_table = $('.yajra-datatable').DataTable({
            processing: true,
            serverSide: true,
            destroy: true,
            pageLength: 100,
            ajax: "{{ url('v2/oms') }}",
            // ajax: currentLocation,
            columns: [{
                    data: 'id',
                    name: 'id',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'code',
                    name: 'code',
                },
                {
                    data: 'status',
                    name: 'status',
                },
                {
                    data: 'active',
                    name: 'active',
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ]
        });

        $(document).on('click', '.remove', function() {
            let bool = confirm('Are you sure you want to delete this ?');
            if (!bool) {
                return false;
            }
        });

    </script>
@stop
