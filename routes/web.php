<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'PageScrapeController@index');

Route::post('/scrape-page', 'PageScrapeController@store');

Route::delete('/{video}/delete', 'PageScrapeController@destroy');

Route::get('/{scrape_item}/log', 'PageScrapeController@log');

Route::prefix('api')->name('api')->group(function () {
    Route::get('/items', 'ScrapeItemController@index');
    // todo is this endpoint in use?
    Route::get('/in-progress', 'ProgressController@index');
});
