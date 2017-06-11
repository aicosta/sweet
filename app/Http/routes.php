<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'HomeController@index')->middleware('auth');
Route::get('/home', 'HomeController@index')->middleware('auth');
Route::auth();



Route::auth();

Route::get('/home', 'HomeController@index');

Route::group(['prefix' => 'products','middleware'=>'auth'], function() {
    //GET's

    Route::get('/create', 'ProductController@create');
    Route::get('/edit/{id}', 'ProductController@edit');
    Route::get('/batch', 'ProductController@massAction');
    Route::get('/priceUpdate', 'ProductController@priceUpdate');

    Route::get('/autocomplete/{term}', 'ProductController@autocomplete');


    Route::get('/getprovider/{id}', 'ProductController@getProvider');
    Route::get('/fullReport', 'ProductController@fullReport');


    //Kits
    Route::get('/kit/create', 'KitController@createView');
    


    //POST's
    Route::post('/create', 'ProductController@saveProduct');
    Route::post('/edit/{id}', 'ProductController@editSave');

    //Updates
    Route::post('/import/products', 'ProductController@importProducts');
    Route::post('/import/stocks', 'ProductController@importStocks');
    Route::post('/import/prices', 'ProductController@importPrices');
    Route::post('/clone', 'ProductController@postClone');


    Route::get('/', 'ProductController@listProducts');
});

Route::group(['prefix' => 'prices','middleware'=>'auth'], function() {
    Route::get('/dev', 'PriceController@dev');
});


Route::group(['prefix' => 'orders','middleware'=>'auth'], function() {
    Route::get('/core', 'OrdersController@fullReport');

    Route::get('/changeStatus/{order}/{status}', 'OrdersController@changeStatus');
    Route::get('/keys', 'OrdersController@syncKeys');
    Route::get('/byStatus/{id?}', 'OrdersController@byStatus');
    Route::get('/itemsFix', 'OrdersController@itemsFix');
    Route::get('/invoiceSync', 'OrdersController@invoiceSync');

    Route::get('/view/{id}', 'OrdersController@view');
    Route::get('/buy-or-invoice/{id?}','OrdersController@buyOrInvoide');
    Route::get('/tamarindos', 'OrdersController@tamarindos');
    Route::get('/list', 'OrdersController@list');
    Route::get('/filter', 'OrdersController@filter');
    Route::get('/export', 'OrdersController@export');
    Route::get('/autoStatus', 'OrdersController@autoStatus');
    Route::get('/clone/{id}', 'ProductController@clone');

    Route::get('/{id?}', 'OrdersController@orders');




    Route::post('/insertShipping', 'OrdersController@insertShipping');
    Route::post('/comment', 'OrdersController@comment');
    

    


});
Route::group(['prefix' => 'mobly','middleware'=>'auth'], function() {
     Route::get('/', 'OrdersController@moblyIndex');
     Route::post('/orders', 'OrdersController@mobly');
     Route::get('/cepMobly', 'OrdersController@cepMobly');

});
Route::group(['prefix' => 'tags','middleware'=>'auth'], function() {

    Route::get('/me2', 'TagsController@getListMe2');
    Route::post('/postcode', 'SigepController@setShippingCode');

    Route::get('/', 'TagsController@getList');
    Route::post('/', 'SigepController@fechaPlp');
});

Route::group(['prefix' => 'users','middleware'=>'auth'], function() {
    Route::get('/list', 'UserController@list');
    Route::get('/edit/{id}', 'UserController@edit');
    Route::get('/status/{user}', 'UserController@status');
});


Route::group(['prefix' => 'api'], function() {
	Route::get('/auth', 'ApiAuthController@auth');
	Route::get('/products/{sku?}', ['before' => 'jwt-auth', 'uses' => 'ApiAuthController@getProducts']);
    Route::get('/toPrint', 'OrdersController@toPrint');
    Route::get('/printed/{id}', 'OrdersController@printed');
    Route::get('/getProvider/{id}', 'ProductController@getProvider');
});






Route::group(['prefix' => 'cnova'], function() {
        Route::get('/orders', 'marketplaces\CnovaController@orders');
        Route::get('/fix', 'marketplaces\CnovaController@fixOrders');
        Route::get('/tracking', 'marketplaces\CnovaController@tracking');
        Route::get('/finish', 'marketplaces\CnovaController@finish');
        Route::get('/stockAll', 'marketplaces\CnovaController@stockAll');
        Route::get('/stock', 'marketplaces\CnovaController@stock');
        Route::get('/price', 'marketplaces\CnovaController@price');
});

Route::group(['prefix' => 'b2w'], function() {
    Route::get('/orders', 'marketplaces\B2wController@orders');
    Route::get('/fix', 'marketplaces\B2wController@fixOrders');
    Route::get('/productFix', 'marketplaces\B2wController@productFix');
    Route::get('/invoice', 'marketplaces\B2wController@invoice');
    Route::get('/shipping', 'marketplaces\B2wController@shipping');
    Route::get('/finish', 'marketplaces\B2wController@finish');
    Route::get('/updateStocks', 'marketplaces\B2wController@updateStocks');
    Route::get('/stockAll', 'marketplaces\B2wController@stockAll');
    Route::get('/stock', 'marketplaces\B2wController@stock');
    Route::get('/price', 'marketplaces\B2wController@price');
    Route::get('/stockReport/{id}', 'marketplaces\B2wController@stockReport');

    Route::get('/canceledOrders', 'marketplaces\B2wController@canceledOrders');

    Route::get('/products', 'marketplaces\B2wController@products');
    Route::get('/sendProducts/{sku}', 'marketplaces\B2wController@sendProducts');
    Route::get('/checkProvider/{provider}', 'marketplaces\B2wController@checkProvider');


});
Route::group(['prefix' => 'marketplaces','middleware'=>'auth'], function() {
    Route::get('/itens', 'marketplaces\MarketplacesController@items');
});
Route::group(['prefix' => 'ml'], function() {
    Route::get('/categorizator', 'marketplaces\MercadoLivreController@categorizator');
    Route::get('/orders', 'marketplaces\MercadoLivreController@orders');
    Route::get('/sendKeys', 'marketplaces\MercadoLivreController@sendMe2');
    Route::get('/fix', 'marketplaces\MercadoLivreController@fixOrders');
    Route::get('/auth', 'marketplaces\MercadoLivreController@auth');
    Route::get('/fixSku', 'marketplaces\MercadoLivreController@fixSku');
    Route::get('/callback', 'marketplaces\MercadoLivreController@callback');
    Route::get('/redirect', 'marketplaces\MercadoLivreController@redirect');
    Route::get('/login', 'marketplaces\MercadoLivreController@login');
    Route::get('/reverseME2', 'marketplaces\MercadoLivreController@reverseME2');
    Route::get('/productSync', 'marketplaces\MercadoLivreController@productSync');
    
    Route::get('/sendProduct/{providerId}','marketplaces\MercadoLivreController@sendProduct');
    Route::get('/sendProductBySku/{sku}','marketplaces\MercadoLivreController@sendProductBySku');
    Route::get('/resendImages','marketplaces\MercadoLivreController@resendImages');
    
    Route::get('/ajax/itens', 'marketplaces\MercadoLivreController@ajaxItens');
    Route::get('/stockAll', 'marketplaces\MercadoLivreController@stockAll');
    Route::get('/syncStatus', 'marketplaces\MercadoLivreController@syncStatus');
    Route::get('/export', 'marketplaces\MercadoLivreController@export');

    Route::get('/stock', 'marketplaces\MercadoLivreController@stock');
    Route::get('/price', 'marketplaces\MercadoLivreController@price');
    Route::get('/checkProvider/{id}', 'marketplaces\MercadoLivreController@checkProvider');

});
Route::group(['prefix' => 'invoice','middleware'=>'auth'], function() {
    Route::get('/{id?}', 'OrdersController@invoice');
    Route::post('/', 'OrdersController@invoiceStart');
});
Route::group(['prefix' => 'out','middleware'=>'auth'], function() {
    Route::get('/waiting', 'ExpedicaoController@invoice');
    Route::post('/', 'OrdersController@invoiceStart');
});


Route::group(['prefix' => 'sigep'], function() {
    Route::get('/fechaplp', 'SigepController@fechaPlp');
    Route::get('/range/{tipo?}/{quantity?}', 'SigepController@range');
    Route::get('/geraDv/{tag}', 'SigepController@geraDv');
    Route::get('/print/{id}/{type}/{format?}', 'SigepController@plpPrinter');
    Route::get('/vipp', 'SigepController@vipp');
    Route::get('/fixTag', 'SigepController@fixTag');
    Route::get('/tracking', 'SigepController@tracking');

});


Route::group(['domain' => 'walmart.fullhub.com.br'], function() {
    Route::get('/ping', function () { return 'pong'; });
    Route::post('/fulfillment-preview', 'marketplaces\WalmartController@fulfillmentPreview');

    Route::get('/stock', 'marketplaces\WalmartController@stock');
    Route::get('/price', 'marketplaces\WalmartController@price');
    Route::get('/stockAll', 'marketplaces\WalmartController@stockAll');


});

Route::group(['prefix' => 'speedboys'], function() {
    Route::get('/run', 'SpeedBoysController@run');
});
Route::group(['prefix' => 'nff','middleware'=>'auth'], function() {
    Route::get('/', 'NffController@index');
    Route::post('/in', 'NffController@in');
    Route::post('/validAndInsert', 'NffController@validAndInsert');

});



Route::group(['prefix' => 'stock','middleware'=>'auth'], function() {
    Route::get('/list', 'StockController@list');
    Route::get('/view/{id}', 'StockController@view');
    Route::get('/create', 'StockController@insert');
    Route::post('/insert', 'StockController@insertStock');
    Route::get('/export', 'StockController@export');
    Route::get('/report/{type}/{term}', 'StockController@report');
    Route::get('/fixStock', 'StockController@fixStock');
    

    Route::get('/all', 'StockController@insertall');
});
Route::group(['prefix' => 'sweet','middleware'=>'auth'], function() {
    Route::get('/dev', 'marketplaces\SweetController@dev');
    Route::get('/fixRegister', 'marketplaces\SweetController@fixRegister');
    Route::get('/fixImages', 'marketplaces\SweetController@fixImages');
    
    
});
Route::group(['prefix' => 'ocurrence','middleware'=>'auth'], function() {
    //Ocurrence/OcurrencesController@
    Route::get('/sector', 'Ocurrence\OcurrencesController@sector');
    Route::get('/sector/create', 'Ocurrence\OcurrencesController@sectorCreate');
});



Route::group(['prefix' => 'extra','middleware'=>'auth'], function() {
    Route::get('/xmlReader', 'FiscalController@xmlReader');
    Route::post('/xmlReaderExport', 'FiscalController@xmlReaderExport');

});


Route::group(['prefix' => 'tamarindos'], function() {
    Route::get('/dev', 'marketplaces\TamarindosController@dev');
});


Route::group(['prefix' => 'dev','middleware'=>'auth'], function() {
    Route::get('/stock/{sku}', 'StockController@getStock');
    Route::get('/getUpdated', 'StockController@getUpdated');
    Route::get('/init', 'StockController@init');
    Route::get('/test/{id?}', 'StockController@test');
    Route::get('/clear-cache', function() {
        $exitCode = Artisan::call('cache:clear');
        dd($exitCode);
    });
    Route::get('/queue', function() {
        $exitCode = Artisan::call('queue:work');
        dd($exitCode);
    });
});