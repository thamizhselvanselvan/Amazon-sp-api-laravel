@extends('adminlte::page')
@section('title', 'Departments')

@section('css')
<link rel="stylesheet" href="/css/styles.css">
<!-- <style>
    .checkBox {
        width: 20px;
        height: 20px;
        margin-top: 9px;
    }
</style> -->
@stop
@section('content_header')
<div class='row'>

    <div class="col text-center">
        <h1 class="mb-1 text-dark font-weight-bold">Manage Departments </h1>
    </div>
</div>

@stop

@section('content')
<div class="card">
    <div class="card-body">
    <a href="{{ route('department.home') }}" class="{{ isset($records) ? 'btn btn-primary btn-sm' : 'd-none' }}">
                <i class="fas fa-arrow-left"></i> back
            </a>
            <!-- <div class="col"> -->
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

        <div class="row">
            <div class="col-sm-8">
                <table class="table table-bordered yajra-datatable table-striped text-center table-sm">
                    <thead class='table-primary'>
                        <tr class="length">
                            <th>ID</th>
                            <th>Department</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <div class="col-sm-4">
                <form action="{{ isset($records) ? route('update.department', $records['id']) : route('department.home') }}" method="POST">
                    @csrf
                    <div class="card">
                        <div class="card-body">
                            <div class="form-group">
                                <x-adminlte-input label="Department" type="text" name="department" placeholder="Department" value="{{ $records['department'] ?? '' }}" />
                            </div>
                            <div class="form-group">
                                <label for="status">Active</label>
                                <input type="checkbox" name="status" class="checkBox" {{ $records['status'] ?? '' == 1 ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <x-adminlte-button label="{{ isset($records) ? 'Update Department' : 'Add  Department' }}" type="submit" theme="{{ isset($records) ? 'success' : 'primary' }}" icon=" {{ isset($records) ? 'fas fa-edit' : 'fa fa-plus' }}" />
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
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
        // searching: false,
        paging: false,
        ajax: "{{ url('v2/master/departments') }}",
        // ajax: currentLocation,
        columns: [{
                data: 'id',
                name: 'id',
                orderable: false,
                searchable: false
            },
            {
                data: 'department',
                name: 'department',
            },
            {
                data: 'status',
                name: 'status',
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