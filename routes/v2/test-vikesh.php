<?php

use Illuminate\Support\Facades\Route;

Route::get('volume', function () {

    $catalogTable = table_model_create(country_code: 'us', model: 'Catalog', table_name: 'catalognew');
    $catalogRecords = $catalogTable->select('asin', 'height', 'length', 'width', 'unit')
        ->get()
        ->toArray();
    po($catalogRecords);
});
