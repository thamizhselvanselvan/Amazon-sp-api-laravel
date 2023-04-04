@extends('adminlte::page')

@section('title', 'POD Templete')

    <style>
         @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap');
        .POD{
            position: relative;
            font-family: 'Roboto';
            width:450px;
        }
        .POD .centered-element {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
        .POD .centered-element img{
            opacity:0.04;
        }
        .POD p{
            font-size: 10px;
        }
        .POD p span{
            font-weight: 700;
        }
        .POD figure h6{
            font-size: 12px;
            font-weight: 700;
        }
        .POD li{
            list-style: none;
            font-size:10px;
            font-weight: 300;
        }
        .POD li span{
            font-weight:500;
            line-height:20px;
        }
        .POD .sign{
            width: 107px;
        }
        .POD .sign .text{
            bottom:0;
            right: 0;
            color:red;
            font-size: 6px;
            font-weight: 700;
        }
    </style>
@section('content')
    <div class="p-3">
        <div class="m-auto my-2 bg-white p-4 POD">
            <div class="centered-element">
                <img src="https://b2cship.us/wp-content/uploads/2022/01/logo-e1643398015181.png"  width="264" alt="centered" />
            </div>
            <h6 class="text-center">Proof Of Delivery</h6>
            <p class="text-center">The following is proof of delivery for Way Bill No: <span>US10033096</span></p>
            <figure>
                <h6>Delivery Information:</h6>
                <figcaption class="row">
                    <li class="col-6"><span>Product Group:</span> EXP</li>
                    <li class="col-6"><span>Product Type:</span> EXP</li>
                    <li class="col-6"><span>Origin:</span> DEL</li>
                    <li class="col-6"><span>Destination:</span> KTK</li>
                </figcaption>
            </figure>
            <figure>
                <h6>Delivery Information:</h6>
                <figcaption class="row">
                    <li class="col-6"><span>Receiver:</span> Vinay</li>
                    <li class="col-6"><span>Attention:</span> M/S</li>
                    <li class="col-6"><span>Delivered To:</span>  VINAY</li>
                </figcaption>
            </figure>
            <figure>
                <h6>Order Details:</h6>
                <figcaption class="row">
                    <li class="col"><span>Pieces:</span> 1</li>
                    <li class="col"><span>Weight:</span> 0.5 KG</li>
                </figcaption>
            </figure>
            <figure>
                <h6>Shipping Information:</h6>
                <figcaption class="row">
                    <li class="col-6"><span>Shipper Ref:</span> 20457868852</li>
                    <li class="col-6"><span>Shipper:</span> BD</li>
                    <li class="col-6"><span>Pickup Date:</span>  March 17, 2023</li>  
                    <li class="col-6"><span>Delivered On:</span> 1 8 / 0 3 / 2 0 2 3(11:58pm)</li>
                </figcaption>
            </figure>
            <div class="d-flex justify-content-end">
                <figure class="position-relative sign">
                    <img src="/image/sign.png" width="107" alt="sign">
                    <figcaption class="text position-absolute">
                        20457868852
                    </figcaption>
                </figure>
            </div>
        </div>
    </div>
@stop