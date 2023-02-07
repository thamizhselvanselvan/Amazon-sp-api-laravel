<?php


use App\Http\Controllers\V2\Masters\RolesPermissionsController;
use App\Http\Controllers\V2\Masters\DepartmentController;
use App\Http\Controllers\V2\Masters\UserController;
use App\Http\Controllers\V2\Masters\CompanyMasterController;
use App\Http\Controllers\V2\Masters\CredentialController;
use App\Http\Controllers\V2\Masters\GeoManagementController;
use App\Http\Controllers\V2\Masters\CurrencyController;
use App\Http\Controllers\V2\Masters\RegionController;
use Illuminate\Support\Facades\Route;



Route::prefix('v2/master/')->group(function () {

    Route::get('/roles', [RolesPermissionsController::class, 'index']);
    // Route::get('/departments', [DepartmentController::class, 'index'])->name('department.home');
    // Route::post('/departments', [DepartmentController::class, 'AddDepartments'])->name('add.department');
    Route::match (['get', 'post'], '/departments',[DepartmentController::class,'index'])->name('department.home');

    Route::prefix('/departments')->group(function () {

        Route::get('/edit/{id}', [DepartmentController::class, 'EditDepartments'])->name('edit.department');
        Route::post('/update/{id}', [DepartmentController::class, 'UpdateDepartments'])->name('update.department');
        Route::get('/remove/{id}', [DepartmentController::class, 'DeleteDepartments'])->name('remove.department');
    });

    // Route::get('/users', [UserController::class, 'index'])->name('user.home');
    // Route::post('/users', [UserController::class, 'save_user'])->name('add.user');
    // Route::match (['get', 'post'], '/users',[UserController::class,'index'])->name('user.home');

    Route::prefix('/users')->group(function () {

        Route::get('/create', [UserController::class, 'create'])->name('create.user');
        Route::get('/{id}/edit', [UserController::class, 'edit'])->name('edit.user');
        Route::post('/update/{id}', [UserController::class, 'update'])->name('update.user');
        Route::get('/{id}/remove', [UserController::class, 'delete']);
        Route::get('/bin', [UserController::class, 'bin'])->name('bin.user');
        Route::get('/restore/{id}', [UserController::class, 'restore']);
    });


    Route::match (['get', 'post'], '/company',[CompanyMasterController::class,'index'])->name('company.home');
    // Route::get('/company', [CompanyMasterController::class, 'index'])->name('company.home');
    // Route::post('/company', [CompanyMasterController::class, 'add'])->name('add.company');

    Route::prefix('/company')->group(function () {
        Route::get('/create', [CompanyMasterController::class, 'create'])->name('create.company');
        Route::get('/edit/{id}', [CompanyMasterController::class, 'edit'])->name('edit.company');
        Route::post('/update/{id}', [CompanyMasterController::class, 'update'])->name('update.company');
        Route::post('/trash/{id}', [CompanyMasterController::class, 'trash'])->name('trash.company');
        Route::get('/trash-view', [CompanyMasterController::class, 'trashView'])->name('trash-view.company');
        Route::post('/restore/{id}', [CompanyMasterController::class, 'restore'])->name('restore.company');
    });

    Route::prefix('/geo')->group(function () {
            // Route::get('/country', [GeoManagementController::class, 'index_country'])->name('geo.country');
            // Route::post('/country', [GeoManagementController::class,'store_country'])->name('geo.store.country');
            Route::match(['get', 'post'],'/country',[GeoManagementController::class, 'index_country'])->name('geo.country');
            // Route::get('/state', [GeoManagementController::class, 'index_state'])->name('geo.state');
            // Route::post('/state', [GeoManagementController::class,'store_state'])->name('geo.store.state');
            Route::match(['get', 'post'],'/state',[GeoManagementController::class, 'index_state'])->name('geo.state');
            
            // Route::get('/city', [GeoManagementController::class, 'index_city'])->name('geo.city');
            // Route::post('/city', [GeoManagementController::class, 'store_city'])->name('geo.store.city');
            Route::match(['get', 'post'],'/city',[GeoManagementController::class, 'index_city'])->name('geo.city');
            

            Route::prefix('/country')->group(function () {
                Route::get('/create', [GeoManagementController::class, 'add_country'])->name('geo.country.create');
                Route::get('/edit/{id}', [GeoManagementController::class,'edit_country']);
                Route::post('/update/{id}', [GeoManagementController::class,'update_country']);
                Route::get('/delete/{id}', [GeoManagementController::class,'destroy_country']);

            });
            Route::prefix('/state')->group(function () {
                Route::get('/create',  [GeoManagementController::class, 'add_state'])->name('geo.state.create');
                Route::get('/edit/{id}', [GeoManagementController::class,'edit_state']);
                Route::post('/update/{id}', [GeoManagementController::class,'update_state']);
                Route::get('/delete/{id}', [GeoManagementController::class,'destroy_state']);
            });
            Route::prefix('/city')->group(function () {
                Route::get('/create',  [GeoManagementController::class, 'add_city'])->name('geo.city.create');
                Route::get('/edit/{id}', [GeoManagementController::class,'edit_city']);
                Route::post('/update/{id}', [GeoManagementController::class,'update_city']);
                Route::get('/delete/{id}', [GeoManagementController::class,'destroy_city']);
                Route::post('/getStates',[GeoManagementController::class,'getStates']);
            });

        });

    Route::prefix('/store')->group(function () {
        Route::match (['get', 'post'], '/currency',[CurrencyController::class,'index'])->name('currency.home');
    
   
    Route::prefix('/currency')->group(function () {
        Route::get('/create',  [CurrencyController::class, 'add'])->name('currency.create');
        Route::get('/edit/{id}', [CurrencyController::class,'edit']);
        Route::post('/update/{id}', [CurrencyController::class,'update'])->name('update.currency');
        Route::post('/delete/{id}', [CurrencyController::class,'delete']);
        Route::get('/trash-view', [CurrencyController::class, 'trashView'])->name('trash-view.currency');
        Route::post('/restore/{id}', [CurrencyController::class, 'restore'])->name('restore.currency');
    });

    Route::match (['get', 'post'], '/regions',[RegionController::class,'index'])->name('regions.home');
    Route::prefix('/regions')->group(function () {
        Route::get('/create',  [RegionController::class, 'add'])->name('regions.create');
        Route::get('/edit/{id}', [RegionController::class,'edit']);
        Route::post('/update/{id}', [RegionController::class,'update'])->name('update.region');
        Route::post('/delete/{id}', [RegionController::class,'delete']);
        Route::get('/trash-view', [RegionController::class, 'trashView'])->name('trash-view.region');
        Route::post('/restore/{id}', [RegionController::class, 'restore'])->name('restore.region');
    });
    Route::match (['get', 'post'], '/credentials',[CredentialController::class,'index'])->name('credentials.home');
        Route::prefix('/credentials')->group(function () {
            Route::get('/create', [CredentialController::class, 'create'])->name('credentials.create');
            Route::get('/edit/{id}', [CredentialController::class,'edit']);
            Route::post('/update/{id}', [CredentialController::class,'update'])->name('update.credentials');
            Route::post('/delete/{id}', [CredentialController::class,'delete']);
            Route::get('/trash-view', [CredentialController::class, 'trashView'])->name('trash-view.credentials');
            Route::post('/restore/{id}', [CredentialController::class, 'restore'])->name('restore.credentials');
        }
        );
});
    Route::match (['get', 'post'], '/users',[UserController::class,'index'])->name('users.home');
    Route::prefix('/users')->group(function () {
        Route::get('/create', [UserController::class, 'create'])->name('users.create');
    }
    );




      

});
