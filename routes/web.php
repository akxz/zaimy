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

//Route::get('/', function () {
//    return view('zaimy');
//});

Route::get('/', 'OfferController@index');
Route::get('/actions/load_cards_for_listings', 'OfferController@filter');
Route::get('/index-base-cards-load', 'OfferController@base_cards');
Route::get('/{pageNotFound1?}/{pageNotFound2?}/{pageNotFound3?}', 'OfferController@notFoundRedirect');
