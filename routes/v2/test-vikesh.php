<?php

use Illuminate\Support\Facades\Route;

Route::get('volume', function () {

    $catalogTable = table_model_create(country_code: 'us', model: 'Catalog', table_name: 'catalognew');
    $catalogRecords = $catalogTable->select('asin', 'height', 'length', 'width', 'unit', 'weight')
        ->get()
        ->toArray();

    // po($catalogRecords);
    foreach ($catalogRecords as $catalogRecord) {
        po($catalogRecord);
        $dimension = $catalogRecord['height'] * $catalogRecord['length'] * $catalogRecord['width'];
        $volumetricOfKg = VolumetricIntoKG($dimension);
        $volumetricOfPounds = VolumetricIntoPounds($dimension);
        po($volumetricOfKg);
        po($volumetricOfPounds);
    }
});
