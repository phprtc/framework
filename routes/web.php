<?php

use App\Http\Controllers\MainController;
use RTC\Contracts\Http\RequestInterface;
use RTC\Http\Router\Route;

Route::get('/', function (RequestInterface $request) {
    $request->getResponse()->html(file_get_contents(dirname(__DIR__) . '/public/index.html'));
});

Route::get('/json', [MainController::class, 'json'])
    ->middleware(['hello', 'test']);

Route::get('/html', [MainController::class, 'html']);

Route::get('/closure', function (RequestInterface $request) {
    $request->getResponse()->json([
        'time' => time(),
        'id' => uniqid(true)
    ]);
});