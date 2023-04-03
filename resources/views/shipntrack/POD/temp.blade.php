@extends('adminlte::page')

@section('title', 'POD Templete')

    <style>
        .POD li{
            list-style: none;
            font-size:0.7rem;
        }
        .POD li span{
            font-weight:600;
            line-height:20px;
        }
        .POD .fw-bold{
            font-weight:600;
        }
        .POD .sign{
            width: 130px;
        }
        .POD .sign .text{
            bottom:0;
            right: 0;
            color:red;
            font-size:0.7rem;
        }
        .POD h6{
            font-size: 0.7rem;
            font-weight: 700;
        }
    </style>
@section('content')
    <div class="p-3">
        <div class="w-75 m-auto my-2 bg-white p-4 POD">
            <div class="pb-3 mb-3 d-flex justify-content-between align-items-center border-bottom">
                <span class="pl-1 fw-bold">Proof Of Delivery</span>
                <img src="https://b2cship.us/wp-content/uploads/2022/01/logo-e1643398015181.png" width="100" alt="B2C Ship by Pacific Impex LLC." id="logo" data-height-percentage="54" data-actual-width="300" data-actual-height="74">
            </div>
            <div class="row m-0">
                <div class="col">
                    <h6>Delivery Information:</h6>
                    <li><span>Way Bill No:</span> US10033096</li>
                    <li><span>Product Group:</span> EXP</li>
                    <li><span>Product Type:</span> EXP</li>
                    <li><span>Origin:</span> DEL</li>
                    <li><span>Destination:</span> KTK</li>
                </div>
                <div class="col">
                    <h6>Receiver Information:</h6>
                    <li><span>Receiver:</span> Vinay</li>
                    <li><span>Attention:</span> M/S</li>
                    <li><span>Delivered To:</span>  VINAY</li>
                </div>
                <div class="col">
                    <h6>Order Details:</h6>
                    <li><span>Pieces:</span> 1</li>
                    <li><span>Weight:</span> 0.5 KG</li>
                </div>
                <div class="col">
                    <h6>Shipping Information:</h6>
                    <li><span>Shipper Ref:</span> 20457868852</li>
                    <li><span>Shipper:</span> BD</li>
                    <li><span>Pickup Date:</span>  March 17, 2023</li>
                    <li><span>Delivered On:</span> 1 8 / 0 3 / 2 0 2 3(11:58pm)</li>
                </div>
            </div>
            <!-- <div class="d-flex justify-content-end">
                <figure class="position-relative sign">
                    <img src="/image/sign.png" width="130" alt="sign">
                    <figcaption class="text position-absolute">
                        20457868852
                    </figcaption>
                </figure>
            </div> -->
        </div>   
    </div>
@stop