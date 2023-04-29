<?php

use App\Http\Controllers\MainController;
use RTC\Contracts\Http\RequestInterface;
use RTC\Http\Router\Route;

$homePage = function (RequestInterface $request) {
    $request->getResponse()->serveHtmlFile(dirname(__DIR__) . '/public/index.html');
};

Route::get('/', $homePage);
Route::get('/index.php', $homePage);
