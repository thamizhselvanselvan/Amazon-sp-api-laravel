<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Symfony\Component\CssSelector\XPath\Extension\FunctionExtension;

Route::get("samsa/test", function () {
   
    $order_id = '402-6642835-7433966';
    DB::connection('web')->update("UPDATE amazoninvoice SET status = 1 WHERE amazon_order_identifier = '$order_id' ");

    $data = DB::connection('web')->select('SELECT * FROM amazoninvoice');
    $date = $data[0]->booking_date;

    $year = date('Y', strtotime($date));
    $month = date('F', strtotime($date));

    $date = $month . '_' . $year;
    echo $year;
    echo '<br>';
    echo $month;
    dd($date);
    //   dd($data);
});
