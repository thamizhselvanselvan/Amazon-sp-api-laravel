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
    <div class="jumbotron jumbotron-fluid">
        <div class="container">
            <h1 class="display-4">{{ $name }}</h1>
            <label class="mr-2" for="doc">Document Name </label>
            {{ $document }}
            <br>
            <label for="front">{{ $document }} Front</label>
            <!-- Large modal -->
            <button type="button" class="btn btn-primary ml-1" data-toggle="modal"
                data-target=".bd-example-modal">View</button>

            <div class="modal fade bd-example-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <img class="m-2" src="{{ $front_path_url }}" alt="{{ $document }} Front">
                    </div>
                </div>
            </div>

            <br>
            <label for="back">{{ $document }} Back</label>
            <!-- Large modal -->
            <button type="button" class="btn btn-primary m-2" data-toggle="modal"
                data-target=".bd-example-modal-lg">View</button>

            <div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <img class="m-2" src="{{ $back_path_url }}" alt="{{ $document }} Back">
                    </div>
                </div>
            </div>

            <p class="lead">
            <form action="/cliqnshop/kyc/update/{{ $id }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="kyc">KYC Status</label>
                    <select id="kyc_status" name="kyc_status" class="form-select ml-2" aria-label="Default select example">

                        <option selected>{{ $kyc_status }}</option>
                        <option value="Rejected">Reject</option>
                        <option value="Accepted">Accept</option>
                    </select>

                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
            </p>
        </div>
    </div>
@stop
