<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Services\AWS_Business_API\Search_Product_API\Search_Product;

Route::get('product/details/search', 'BuisnessAPI\SearchProductRequestController@searchproductRequest');
Route::resource('business/search/products', 'BuisnessAPI\SearchProductRequestController');

Route::get('buisness/product/details', 'BuisnessAPI\ProductsRequestController@productRequestasin');
Route::resource('business/products/request', 'BuisnessAPI\ProductsRequestController');

Route::get('buisness/product/offers', 'BuisnessAPI\searchOffersRequestController@searchoffersproduct');
Route::resource('business/offers', 'BuisnessAPI\searchOffersRequestController');

Route::get('business/asin/details', 'BuisnessAPI\GetProductsByAsinsController@searchasinproduct');
Route::resource('business/byasins', 'BuisnessAPI\GetProductsByAsinsController');

Route::resource('business/details', 'BuisnessAPI\ProductDetailsController');
Route::get('buisness/details', 'BuisnessAPI\ProductDetailsController@viewpro');

Route::get('business/orders/details', 'BuisnessAPI\OrdersController@orderpending')->name('business.orders.pending.list');
Route::get('business/orders/view', 'BuisnessAPI\OrdersController@getorders');
Route::get('business/orders/pending', 'BuisnessAPI\OrdersController@orderspending');
Route::get('business/offers_view', 'BuisnessAPI\OrdersController@prodoffers');
Route::get('business/order/book', 'BuisnessAPI\OrdersController@orderbooking');

Route::get('business/booked/details', 'BuisnessAPI\OrdersController@booked');
Route::get('business/booked/list', 'BuisnessAPI\OrdersController@booked')->name('business.orders.booked.list');

Route::get('business/orders/confirm', 'BuisnessAPI\OrdersController@confirmation');
Route::get('business/orders/confirm/list', 'BuisnessAPI\OrdersController@confirmation')->name('business.orders.confirm.list');
Route::get('business/ship/confirmation', 'BuisnessAPI\OrdersController@notification');
Route::get('business/orders/shipment/list', 'BuisnessAPI\OrdersController@notification')->name('business.orders.shipment.list');

Route::resource('business/orders', 'BuisnessAPI\OrdersController');

Route::get('cliqnshop/kyc', 'Cliqnshop\CliqnshopKycController@kyc_index')->name('cliqnshop.kyc');
Route::get('cliqnshop/kyc/details', 'Cliqnshop\CliqnshopKycController@kyc_details')->name('cliqnshop.kyc.view');
Route::get('cliqnshop/kyc/update', 'Cliqnshop\CliqnshopKycController@kyc_status')->name('cliqnshop.kyc.update');

Route::get('cliqnshop/contact', 'Cliqnshop\ContactListController@contactlist')->name('cliqnshop.contacted');
Route::get('cliqnshop/contact/list', 'Cliqnshop\ContactListController@contactlist')->name('cliqnshop.contacted.list');

Route::get('cliqnshop/banner', 'Cliqnshop\ImageBrandController@threebanner')->name('cliqnshop.banner');
Route::post('cliqnshop/banner/image', 'Cliqnshop\ImageBrandController@storeimage')->name('cliqnshop.image.store');

Route::get('cliqnshop/brand', 'Cliqnshop\ImageBrandController@topselling')->name('cliqnshop.brand');
Route::post('cliqnshop/brand/store', 'Cliqnshop\ImageBrandController@storeasin')->name('cliqnshop.brand.store');

Route::get('cliqnshop/two_banners', 'Cliqnshop\ImageBrandController@twobannersection')->name('cliqnshop.twobanners');
Route::post('cliqnshop/2banners/store', 'Cliqnshop\ImageBrandController@two_bannerstore')->name('cliqnshop.two.banner.store');

Route::get('cliqnshop/one_banners', 'Cliqnshop\ImageBrandController@onebanner')->name('cliqnshop.onebanner');
Route::post('cliqnshop/1banners/store', 'Cliqnshop\ImageBrandController@one_bannerstore')->name('cliqnshop.one.banner.store');

Route::get('cliqnshop/trending', 'Cliqnshop\ImageBrandController@trendingbrandssection')->name('cliqnshop.trending');
Route::post('cliqnshop/trending/store', 'Cliqnshop\ImageBrandController@trendingbrands')->name('cliqnshop.trending.store');

Route::get('cliqnshop/promo', 'Cliqnshop\ImageBrandController@promobanner')->name('cliqnshop.promo.banner');
Route::post('cliqnshop/promo/store', 'Cliqnshop\ImageBrandController@promostore')->name('cliqnshop.promo.banner.store');



Route::get('cliqnshop/catalog/csv', 'Catalog\CliqnshopCatalogController@asinCsvDownload')->name('cliqnshop.catalog.csv.templete');
Route::post('cliqnshop/catalog/csv/import', 'Catalog\CliqnshopCatalogController@cliqnshopImport')->name('cliqnshop.catalog.csv.import');
Route::post('cliqnshop/catalog/textarea/import', 'Catalog\CliqnshopCatalogController@cliqnshoptextarea')->name('cliqnshop.catalog.textarea.import');





Route::get('catalog/cliqnshop/new_asin', 'Catalog\CliqnshopCatalogController@uploaded_export_download')->name('uploaded.asin.catalog.export.cliqnshop');
Route::get('uploaded/catalog/cliqnshop/download/{index}', 'Catalog\CliqnshopCatalogController@Download_uploaded_asin_catalog')->name('uploaded.asin.catalog.export.cliqnshop.download');
Route::get('cliqnshop/db/upload', 'Catalog\CliqnshopCatalogController@insertCliqnshop')->name('cliqnshop.catalog.db.upload');
Route::get('cliqnshop/category-poc', 'Catalog\CategoryController@index')->name('cliqnshop.category-poc');
Route::post('cliqnshop/category-export', 'Catalog\CategoryController@export')->name('cliqnshop.category-export');


Route::get('cliqnshop/category-download', function () {
    $exportFilePath = 'test/Categories.csv';
    if (!Storage::exists($exportFilePath)) {
        Storage::put($exportFilePath, '');
    }
    return Storage::download($exportFilePath);
});

Route::get('cliqnshop/footercontent', 'Cliqnshop\FooterController@index')->name('cliqnshop.footercontent');
Route::post('cliqnshop/footercontent', 'Cliqnshop\FooterController@store')->name('cliqnshop.footercontent.store');


Route::get('cliqnshop/staticpagecontent', 'Cliqnshop\FooterController@staticpagecontent')->name('cliqnshop.footercontent.staticpagecontent');
Route::post('cliqnshop/staticpagecontent', 'Cliqnshop\FooterController@store')->name('cliqnshop.footercontent.store');
Route::post('cliqnshop/getstaticpagecontent', 'Cliqnshop\FooterController@getStaticPageContent')->name('cliqnshop.footercontent.getStaticPageContent');



Route::get('cliqnshop/keyword/log', 'Cliqnshop\CliqnshopKeywordController@keyword_search_log_index')->name('cliqnshop.keyword.log');
Route::post('cliqnshop/keyword/log/delete', 'Cliqnshop\CliqnshopKeywordController@keyword_search_log_remove')->name('cliqnshop.keyword.log.delete');

Route::get('cliqnshop/keyword/ban', 'Cliqnshop\CliqnshopKeywordController@ban_keywords_index')->name('cliqnshop.keyword.ban');
Route::get('cliqnshop/keyword/ban/{site_id}', 'Cliqnshop\CliqnshopKeywordController@ban_keywords_index')->name('cliqnshop.keyword.ban');
Route::post('cliqnshop/keyword/ban', 'Cliqnshop\CliqnshopKeywordController@store_ban_keywords')->name('cliqnshop.keyword.ban');
Route::post('cliqnshop/keyword/ban/edit/{id}', 'Cliqnshop\CliqnshopKeywordController@update_keyword')->name('cliqnshop.keyword.ban.update');
Route::post('cliqnshop/keyword/ban/delete/{id}', 'Cliqnshop\CliqnshopKeywordController@delete_keyword')->name('cliqnshop.keyword.ban.delete');

Route::get('cliqnshop/category', 'Cliqnshop\CliqnshopCategoryController@mshop_category_lister')->name('cliqnshop.category');
Route::post('cliqnshop/category/storebancategory', 'Cliqnshop\CliqnshopCategoryController@storebancategory')->name('cliqnshop.category.storebancategory');

Route::get('cliqnshop/brand/ban', 'Cliqnshop\CliqnshopBrandController@ban_Brands_index')->name('cliqnshop.brand.ban');
Route::get('cliqnshop/brand/ban/{site_id}', 'Cliqnshop\CliqnshopBrandController@ban_Brands_index')->name('cliqnshop.brand.ban');
Route::post('cliqnshop/brand/ban', 'Cliqnshop\CliqnshopBrandController@store_ban_Brands')->name('cliqnshop.brand.ban');
Route::post('cliqnshop/brand/ban/edit/{id}', 'Cliqnshop\CliqnshopBrandController@update_Brand')->name('cliqnshop.brand.ban.update');
Route::post('cliqnshop/brand/ban/delete/{id}', 'Cliqnshop\CliqnshopBrandController@delete_Brand')->name('cliqnshop.brand.ban.delete');


Route::get('cliqnshop/brand/missing', 'Cliqnshop\MissingCatalogDetailsController@brand_missing_index')->name('cliqnshop.brand.missing');
Route::get('cliqnshop/brand/missing/{site_id}', 'Cliqnshop\MissingCatalogDetailsController@brand_missing_index')->name('cliqnshop.brand.missing');

Route::get('cliqnshop/category/missing', 'Cliqnshop\MissingCatalogDetailsController@category_missing_index')->name('cliqnshop.category.missing');
Route::get('cliqnshop/category/missing/{site_id}', 'Cliqnshop\MissingCatalogDetailsController@category_missing_index')->name('cliqnshop.category.missing');

Route::get('product/test', function () {
  // $data[] = $key;
  $searchKey = 'notebook';
  $ApiCall = new Search_Product();
  $result = $ApiCall->SearchProductByKey($searchKey);
});

