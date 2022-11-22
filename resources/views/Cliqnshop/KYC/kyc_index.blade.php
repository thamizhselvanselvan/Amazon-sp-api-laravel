@extends('adminlte::page')

@section('title', 'Cliqnshop KYC Details')

@section('content_header')

    <div class="row">
        <h3>Cliqnshop KYC Details</h3>

    </div>
@stop

@section('content')
@if (session()->has('success'))
        <div class="alert alert-success" role="alert">
            {{ session()->get('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-warning" role="alert">
            {{ session()->get('error') }}
        </div>
    @endif
    <table class="table table-bordered data-table">
        <thead>
            <tr class ="bg-info"> 
                <th>No</th>
                <th>Customer ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Mobile No.</th>
                <th width="100px">Action</th>
            </tr>
        </thead>
        <tbody>

        </tbody>
    </table>
    </div>
@stop

@section('js')
    <script type="text/javascript">
        $(function() {

            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('cliqnshop.kyc') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'telephone',
                        name: 'telephone'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

        });
    </script>

@stop
