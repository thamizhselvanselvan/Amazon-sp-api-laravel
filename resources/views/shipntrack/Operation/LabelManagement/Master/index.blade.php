@extends('adminlte::page')
@section('title', 'Label Master')

@section('css')

@stop

@section('content_header')
    <div class="row">
        <div class="col">
            <h1>ShipNTrack Label Master</h1>
        </div>
        <div class="col ">
            <x-adminlte-button label="Add Records" class="btn btn-info btn-sm float-right" data-toggle="modal"
                data-target="#sntLabelMaster" icon="fa fa-plus-circle" theme="info" />
        </div>
    </div>
    <div id="sntLabelMaster" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><b>ShipNTrack Label Master</b></h4>
                    <button type="button" class="close btn-danger" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="card ">

                        <div class="card-body">
                            <form action="{{ route('shipntrack.label.master.submit') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <x-adminlte-input label="Source" name="source" igroup-size="md" type="text"
                                    placeholder="Source" fgroup-class="col-md-12" maxlength="3" id="source"
                                    autocomplete="off" required />

                                <x-adminlte-input label="Destination" name="destination" igroup-size="md" type="text"
                                    placeholder="Destination" fgroup-class="col-md-12" id="destination" autocomplete="off"
                                    required />

                                <x-adminlte-input label="Upload Logo" type="file" name="logo" igroup-size="md"
                                    placeholder="Choose a file..." fgroup-class="col-md-12">
                                    <x-slot name="prependSlot">
                                        <div class="input-group-text bg-lightblue">
                                            <i class="fas fa-upload"></i>
                                        </div>
                                    </x-slot>
                                </x-adminlte-input>

                                <x-adminlte-textarea label="Return Address" name="return_address" igroup-size="md"
                                    placeholder="Enter Return Address" fgroup-class="col-md-12" autocomplete="off"
                                    required />

                                <x-adminlte-button class="btn-sm float-right mr-2" type="submit" label="Submit"
                                    theme="success" icon="fas fa-lg fa-save" />

                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">Close</button>
                </div>
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

    <div class="modal hide fade" id="form_edit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><b>ShipNTrack Label Master</b></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card ">

                        <div class="card-body">
                            <form action="{{ route('shipntrack.label.master.edit') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" class='id' name="id" />
                                <x-adminlte-input label="Source" name="source" igroup-size="md" type="text"
                                    placeholder="Source" fgroup-class="col-md-12" maxlength="3" class="source"
                                    autocomplete="off" />

                                <x-adminlte-input label="Destination" name="destination" igroup-size="md" type="text"
                                    placeholder="Destination" fgroup-class="col-md-12" class="destination"
                                    autocomplete="off" />

                                <x-adminlte-input label="Upload Logo" type="file" name="logo" igroup-size="md"
                                    placeholder="Choose a file..." fgroup-class="col-md-12" class="logo">
                                    <x-slot name="prependSlot">
                                        <div class="input-group-text bg-lightblue">
                                            <i class="fas fa-upload"></i>
                                        </div>
                                    </x-slot>
                                </x-adminlte-input>

                                <x-adminlte-textarea label="Return Address" name="return_address" igroup-size="md"
                                    placeholder="Enter Return Address" fgroup-class="col-md-12" autocomplete="off"
                                    class="return_address" />

                                <x-adminlte-button class="btn-sm float-right mr-2" type="submit" label="Update"
                                    theme="success" icon="fas fa-lg fa-save" id="update" />

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <table class="table table-striped yajra-datatable table-bordered text-center table-sm mt-2">

        <thead class="table-info">
            <th>Source</th>
            <th>Destination</th>
            <th>Logo</th>
            <th>Return Address</th>
            <th>Action</th>
        </thead>

    </table>
@stop

@section('js')
    <script>
        $('#source').keyup(function() {
            this.value = this.value.toUpperCase();
        });

        $('#destination').keyup(function() {
            this.value = this.value.toUpperCase();
        });

        $('.source').keyup(function() {
            this.value = this.value.toUpperCase();
        });

        $('.destination').keyup(function() {
            this.value = this.value.toUpperCase();
        });

        $(document).on('click', '#update', function() {
            let bool = confirm('Are you sure you want to update this ?');
            if (!bool) {
                return false;
            }
        })

        let yajra_table = $('.yajra-datatable').DataTable({

            processing: true,
            serverSide: true,
            ajax: "{{ route('shipntrack.label.master.index') }}",
            pageLength: 40,
            searching: false,
            bLengthChange: false,
            columns: [{
                    data: 'source',
                    name: 'source',
                    orderable: false,
                    searchable: false,
                },
                {
                    data: 'destination',
                    name: 'destination',
                    orderable: false,
                    searchable: false,
                },
                {
                    data: 'logo',
                    name: 'logo',
                    orderable: false,
                    searchable: false,
                },
                {
                    data: 'return_address',
                    name: 'return_address',
                    orderable: false,
                    searchable: false,
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ],
        });



        $(document).on('click', '#edit_form', function() {

            let id = $(this).data('id');
            let source = $(this).data('source');
            let address = $(this).data('address');
            let destination = $(this).data('destination');

            $('.id').val(id);
            $('.source').val(source);
            $('.destination').val(destination);
            $('.return_address').val(address);

            $('#form_edit').modal('show');
        });

        $('.close').click(function() {
            $('#form_edit').modal('hide');
        });
    </script>
@stop
