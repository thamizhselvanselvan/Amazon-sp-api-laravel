<?php

use App\Http\Controllers\PMSPHPUnitTestController;
use Illuminate\Support\Facades\Route;

Route::get('catalog/dashboard', 'Catalog\CatalogDashboardController@Metrics')->name('catalog.dashboard');
Route::get('catalog/dashboard/update', 'Catalog\CatalogDashboardController@DashboardUpdate')->name('catalog.dashboard.update');

Route::resource('textiles', 'textilesController');
Route::post('import-csv', 'textilesController@importTextiles')->name('import.csv');
Route::get('export_to_csv', 'textilesController@exportTextilesToCSV')->name('export.csv');
Route::get('universalTextiles_download', 'textilesController@download_universalTextiles')->name('download.universalTextiles');

Route::get('file_downloads', 'filedownloads\FileDownloadsController@filedownloads')->name('file.downloads');


Route::get('product/amazon_com', 'product\productController@index')->name('product.amazon_com');
Route::get('product/fetch_from_amazon', 'product\productController@fetchFromAmazon')->name('product.fetch.amazon');
Route::get('product/getPricing', 'product\productController@amazonGetPricing')->name('amazon.getPricing');

Route::get('other-product/amazon_com', 'otherProduct\anotherAmazonProductController@index');
Route::post('other-product/export', 'otherProduct\anotherAmazonProductController@exportOtherProduct')->name('export.other-product');
Route::get('other_file_download', 'otherProduct\anotherAmazonProductController@other_file_download')->name('file.other_file_download');
Route::get('other-product/download/{id}', 'otherProduct\anotherAmazonProductController@download_other_product')->name('download.other-product');
Route::get('other-product/asin_upload', 'otherProduct\anotherAmazonProductController@asinUpload');
Route::post('other-product/asin_save', 'otherProduct\anotherAmazonProductController@asinSave');
Route::post('other-product/add-bulk-asin', 'otherProduct\anotherAmazonProductController@asinTxtSave');

Route::get('other-product/amazon_in', 'otherProduct\OtherAmazonInProductController@index')->name('product.amazon_in');
Route::post('other-product/export_in', 'otherProduct\OtherAmazonInProductController@exportOtherProductIn')->name('export.other-product-in');
Route::get('other-prouduct/download_in', 'otherProduct\OtherAmazonInProductController@other_file_download_in');
Route::get('other-product/file_download_in/{id}', 'otherProduct\OtherAmazonInProductController@download_other_product')->name('download.other-product-in');
Route::get('other-product/asin_upload_in', 'otherProduct\OtherAmazonInProductController@asinUpload');
Route::post('other-product/asin_save_in', 'otherProduct\OtherAmazonInProductController@asinSave');
Route::post('other-product/add_bulk_asin_in', 'otherProduct\OtherAmazonInProductController@asinTxtSave');


Route::get('catalog/asin-source', 'Catalog\AsinSourceController@index')->name('catalog.asin.master');
Route::get('catalog/add-asin', 'Catalog\AsinSourceController@addAsin');
Route::get('catalog/import-bulk-asin', 'Catalog\AsinSourceController@importBulkAsin');
Route::get('catalog/asin-export', 'Catalog\AsinSourceController@exportAsinToCSV')->name('catalog.asin.export');
Route::post('catalog/add-bulk-asin', 'Catalog\AsinSourceController@addBulkAsin')->name('catalog.asin.import');
Route::get('asinMaster_download', 'Catalog\AsinSourceController@download_asin_master')->name('catalog.download.asinMaster');
Route::get('catalog/edit-asin/{id}', 'Catalog\AsinSourceController@editasin');
Route::put('catalog/update/{id}', 'Catalog\AsinSourceController@update')->name('catalog.update.asin');
Route::post('catalog/remove/asin/{id}', 'Catalog\AsinSourceController@trash');
Route::get('catalog/asin/bin', 'Catalog\AsinSourceController@trashView')->name('catalog.softDelete.view');
Route::post('catalog/asin/restore/{id}', 'Catalog\AsinSourceController@restore')->name('catalog.restore.view');
Route::get('catalog/asin-template-download', 'Catalog\AsinSourceController@AsinTemplateDownload')->name('catalog.download.template');
// Route::get('catalog/rate-exchange', 'Catalog\AsinSourceController@getExchangeRate')->name('catalog.exchange.rate');
Route::get('catalog/asin-truncate', 'Catalog\AsinSourceController@AsinTruncate')->name('catalog.asin.source.truncate');

Route::get('catalog/product', 'Catalog\CatalogProductController@Index');
Route::get('catalog/product/fetch-from-amazon', 'Catalog\CatalogProductController@Amazon')->name('catalog.amazon.product');
Route::get('catalog/price/export', 'Catalog\CatalogProductController@PriceExport')->name('catalog.price.export');
Route::get('catalog/export', 'Catalog\CatalogProductController@ExportCatalog')->name('catalog.export');
Route::get('catalog/get-file', 'Catalog\CatalogProductController@GetCatalogFile')->name('catalog.get.download.file');
Route::get('catalog/download/csv-file/{country_code}/{priority}', 'Catalog\CatalogProductController@DownloadCatalogIntocsv');
Route::get('catalog/download/price/{country_code}/{priority}', 'Catalog\CatalogProductController@DownloadCatalogPrice');
Route::POST('catalog/asin/search', 'Catalog\CatalogProductController@CatalogSearch')->name('catalog.asin.search');

Route::get('catalog/export-with-price', 'Catalog\CatalogProductController@CatalogWithPrice')->name('catalog.with.price');
Route::POST('catalog/catalog-with-price', 'Catalog\CatalogProductController@CatalogWithPriceExport')->name('catalog.with.price.export');
Route::POST('catalog/asin/upload', 'Catalog\CatalogProductController@CatalogWithPriceAsinUpload')->name('catalog.with.price.asin.upload');
Route::get('catalog/with-price', 'Catalog\CatalogProductController@CatalogWithPriceFileShow')->name('catalog.with.price.file.show');
Route::get('catalog/with-price/download/template', 'Catalog\CatalogProductController@CatalogWithPriceDownloadTemplate')->name('catalog.with.price.download.template');
Route::get('catalog/with-price/download/csv/{country_code}/{priority}', 'Catalog\CatalogProductController@CatalogWithPriceDownload')->name('catalog.with.price.download');

Route::get('catalog/asin-destination', 'Catalog\AsinDestinationController@index')->name('Asin.destination.index');
Route::get('catalog/import-asin-destination', 'Catalog\AsinDestinationController@AsinDestinationImport');
Route::post('catalog/asin-destination-file', 'Catalog\AsinDestinationController@AsinDestinationFile')->name('catalog.asin.destination.file');
Route::get('catalog/edit-asin-destination/{id}', 'Catalog\AsinDestinationController@AsinDestinationEdit');
Route::post('catalog/update-asin-destination/{id}', 'Catalog\AsinDestinationController@AsinDestinationUpdate')->name('catalog.update.asin.destination');
Route::get('catalog/trash-asin-destination/{id}', 'Catalog\AsinDestinationController@AsinDestinationTrash');
Route::get('catalog/asin-destination/bin', 'Catalog\AsinDestinationController@AsinDestinationTrashView')->name('catalog.asin.destination.bin');
Route::get('catalog/asin-destination/restore/{id}', 'Catalog\AsinDestinationController@AsinDestinationTrashRestore');
Route::get('catalog/asin-destination/download-template', 'Catalog\AsinDestinationController@AsinDestinationDownloadTemplate')->name('catalog.destination.download.template');
Route::get('catalog/asin-destination/asin-export', 'Catalog\AsinDestinationController@AsinDestinationAsinExport')->name('catalog.asin.dastination.export');
Route::get('catalog/asin-destination/download-csv', 'Catalog\AsinDestinationController@AsinDestinationDownloadCsvZip')->name('catalog.download.asin.destination');
Route::get('catalog/asin-destination/truncate', 'Catalog\AsinDestinationController@AsinDestinationBBTruncate')->name('catalog.asin.destination.truncate');
Route::post('catalog/asin-destination/search-delete', 'Catalog\AsinDestinationController@AsinDestinationBBSearchDelete')->name('catalog.asin.destination.search.delete');

Route::get('catalog/exchange-rate', 'Catalog\CatalogExchangeManagementController@index');
Route::post('catalog/update/exchange-rate', 'Catalog\CatalogExchangeManagementController@CatalogUpdate')->name('catalog.update.exchange.rate');
Route::get('catalog/record/auto-load', 'Catalog\CatalogExchangeManagementController@CatalogRecordAutoload')->name('catalog.record.auto.load');
Route::get('catalog/buybox/prie/recalculate', 'Catalog\CatalogExchangeManagementController@CatalogBuyBoxPriceRecalculate')->name('catalog.buybox.price.recalculate');

Route::get('catalog/index', 'Catalog\CliqnshopCatalogController@index')->name('cliqnshop.catalog.index');
Route::get('catalog/cliqnshop/export', 'Catalog\CliqnshopCatalogController@catalogexport')->name('catalog.export.cliqnshop');
Route::get('catalog/cliqnshop/get-file', 'Catalog\CliqnshopCatalogController@exportdownload')->name('catalog.export.download.cliqnshop');
Route::get('catalog/cliqnshop/download/{index}', 'Catalog\CliqnshopCatalogController@DownloadCatalogcloqnshop')->name('catalog.export.cliqnshop.download');

Route::get('catalog/destination/file/monitor', 'Catalog\AsinDestinationController@DestinationFileManagementMonitor')->name('destination.file.management.monitor');
Route::get('catalog/source/file/monitor', 'Catalog\AsinSourceController@SourceFileManagementMonitor')->name('source.file.management.monitor');
Route::get('catalog/price/file/monitor', 'Catalog\CatalogProductController@FileManagementMonitor')->name('catalog.export.file.management.monitor');

Route::match(['get', 'post'], 'catalog/export/all-price', 'Catalog\CatalogProductController@ExportAllPrice')->name('catalog.export.all-price');

Route::get('catalog/buybox/import', 'Catalog\BuyBoxImportExportController@index')->name('catalog.buybox.import.home');
Route::match(['get', 'post'], 'catalog/buybox/upload', 'Catalog\BuyBoxImportExportController@BuyBoxUploadFile')->name('catalog.buybox.upload.file');
Route::get('catalog/buybox/file/monitor', 'Catalog\BuyBoxImportExportController@BuyBoxFileManagementMonitor')->name('buybox.file.management.monitor');

Route::get('catalog/buybox/export', 'Catalog\BuyBoxImportExportController@ExportIndex')->name('catalog.buybox.export.home');
Route::get('catalog/buybox/download/export/template', 'Catalog\BuyBoxImportExportController@DownloadBuyBoxTemplate')->name('catalog.buybox.download.export.template');
Route::match(['get', 'post'], 'catalog/buybox/export/csv', 'Catalog\BuyBoxImportExportController@ExportBuyBox')->name('catalog.buybox.export.csv');
Route::get('catalog/buybox/download/file', 'Catalog\BuyBoxImportExportController@GetBuyBoxFile')->name('catalog.buybox.file.download');
Route::get('catalog/buybox/download/zip/{folder}/{countryCode}/{priority}', 'Catalog\BuyBoxImportExportController@DownloadBuyBoxFile');

Route::get('catalog/buybox/count', 'Catalog\BuyBoxImportExportController@BuyBoxSellerTableCount')->name('catalog.buybox.count');
Route::post('catalog/buybox/truncate', 'Catalog\BuyBoxImportExportController@BuyBoxTruncate')->name('catalog.buybox.truncate');


// Route::POST('catalog/test/progress', 'Catalog\CliqnshopCatalogController@progress')->name('test.progress');
