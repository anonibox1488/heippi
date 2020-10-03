<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('verify', 'UserController@verify');
Route::post('registro', 'UserController@store');
Route::post('login', 'UserController@login');
Route::get('types', 'UserController@getTypes');
Route::get('hospitals', 'UserController@getHospitals');
Route::post('password/email', 'UserController@resetPassword');
Route::post('password/reset', 'UserController@changePassword');


Route::group(['middleware'=> 'auth.jwt'], function(){
	Route::post('logout', 'UserController@logout');
	Route::post('registro/doctor', 'UserController@storeDoctor');
	Route::get('patients', 'UserController@getPatients');
	Route::post('observations', 'ObservationController@store')->middleware('candoctor');
	Route::get('observations', 'ObservationController@getObservations');
	Route::get('observations/xls', 'ObservationController@getXlsObservations');
	
});

