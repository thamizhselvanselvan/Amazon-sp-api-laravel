@extends('adminlte::page')

@section('title', 'B2C POD')

@section('content_header')
    <div class="row">
        <h5 class="col fw-bolder">B2C Proof Of Delivery</h5>
    </div>
@stop

@section('content')
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="destination" value="IN" data-bs-toggle="modal" data-bs-target="#AWB_modal" data-bs-whatever="@mdo">
        <label class="form-check-label" for="inlineRadio1">Enter AWB</label>
    </div>
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="destination" value="IN" data-bs-toggle="modal" data-bs-target="#Order_id_modal" data-bs-whatever="@mdo">
        <label class="form-check-label" for="inlineRadio1">Enter Order ID</label>
    </div>

    <div class="modal fade" id="AWB_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">B2C Proof Of Delivery</h5>
                    <button type="button" class="btn-close close-modal border-0 bg-danger rounded-circle" data-bs-dismiss="modal" aria-label="Close">X</button>
                </div>
                <div class="modal-body">
                    <nav>
                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                            <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#AWB-text" type="button" role="tab" aria-controls="nav-home" aria-selected="true">Text area</button>
                            <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#AWB-import" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">Import CSV</button>
                        </div>
                    </nav>
                    <div class="tab-content" id="nav-tabContent">
                        <div class="tab-pane fade show active py-2" id="AWB-text" role="tabpanel" aria-labelledby="nav-home-tab">
                            <div class="mb-3">
                                <label for="exampleFormControlTextarea1" class="form-label">AWB By Text-area</label>
                                <textarea class="form-control" id="exampleFormControlTextarea1" rows="3" placeholder="25 AWB Accepted at a time"></textarea>
                            </div>
                            <button type="button" class="btn btn-sm btn-success" data-bs-dismiss="modal">Download Templete</button>
                            <button type="button" class="btn btn-sm btn-primary" data-bs-dismiss="modal">Submit</button>
                        </div>
                        <div class="tab-pane fade" id="AWB-import" role="tabpanel" aria-labelledby="nav-profile-tab">
                            <div class="mb-3">
                                <label for="formFile" class="form-label">Import AWB CSV</label>
                                <input class="form-control" type="file" id="formFile">
                            </div>
                            <button type="button" class="btn btn-sm btn-info" data-bs-dismiss="modal">Submit</button>
                            <button type="button" class="btn btn-sm btn-success" data-bs-dismiss="modal">Download Templete</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="Order_id_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">B2C Proof Of Delivery</h5>
                    <button type="button" class="btn-close close-modal border-0 bg-danger rounded-circle" data-bs-dismiss="modal" aria-label="Close">X</button>
                </div>
                <div class="modal-body">
                    <nav>
                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                            <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#text-area" type="button" role="tab" aria-controls="nav-home" aria-selected="true">Text area</button>
                            <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#import-csv" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">Import CSV</button>
                        </div>
                    </nav>
                    <div class="tab-content" id="nav-tabContent">
                        <div class="tab-pane fade show active py-2" id="text-area" role="tabpanel" aria-labelledby="nav-home-tab">
                            <div class="mb-3">
                                <label for="exampleFormControlTextarea1" class="form-label">Order ID By Text-area</label>
                                <textarea class="form-control" id="exampleFormControlTextarea1" rows="3" placeholder="25 Order Id Accepted at a time"></textarea>
                            </div>
                            <button type="button" class="btn btn-sm btn-primary" data-bs-dismiss="modal">Submit</button>
                            <button type="button" class="btn btn-sm btn-success" data-bs-dismiss="modal">Download</button>
                        </div>
                        <div class="tab-pane fade" id="import-csv" role="tabpanel" aria-labelledby="nav-profile-tab">
                            <div class="mb-3">
                                <label for="formFile" class="form-label">Import Order ID CSV</label>
                                <input class="form-control" type="file" id="formFile">
                            </div>
                            <button type="button" class="btn btn-sm btn-primary" data-bs-dismiss="modal">Submit</button>
                            <button type="button" class="btn btn-sm btn-success" data-bs-dismiss="modal">Download</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

<style>
    .close-modal{
        font-size: 10px;
        width: 20px;
        height: 20px;
    }
</style>




