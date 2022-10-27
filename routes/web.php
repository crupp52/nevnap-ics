<?php

use App\Services\NameDaysDataService;
use Illuminate\Http\Request;
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

Route::get('/load', function () {
    /** @var NameDaysDataService  $nameDaysDataService */
    $nameDaysDataService = app(NameDaysDataService::class);
    $result = $nameDaysDataService->getData();
    $nameDaysDataService->saveToDatabase($result);
});

Route::get('/', function (Request $request) {
    /** @var NameDaysDataService  $nameDaysDataService */
    $nameDaysDataService = app(NameDaysDataService::class);

    if ($request->has('name')){
        $result = $nameDaysDataService->findMainByName($request->get('name'));
    }elseif ($request->has('date')){
        $result = $nameDaysDataService->findMainByDate($request->get('date'));
    }else{
        $result = null;
    }

    dd($result);
});

Route::get('/autocomplete', function (Request $request) {
    /** @var NameDaysDataService  $nameDaysDataService */
    $nameDaysDataService = app(NameDaysDataService::class);
    return json_encode($nameDaysDataService->autocomplete($request->get('name', null)));
});
