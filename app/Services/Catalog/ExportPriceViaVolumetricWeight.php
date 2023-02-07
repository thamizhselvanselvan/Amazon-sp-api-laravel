<?php

namespace App\Services\Catalog;

class ExportPriceViaVolumetricWeight
{
    private $countryCode;
    private $priority;

    public function index($countryCode, $fmID, $priority)
    {
        $this->countryCode = strtoupper($countryCode);
    }
}
