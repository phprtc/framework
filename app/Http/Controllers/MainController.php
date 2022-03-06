<?php

namespace App\Http\Controllers;

use RTC\Http\Controller;

class MainController extends Controller
{
    public function index(): void
    {
        $this->response->plain('Hello World');
    }

    public function json(): void
    {
        $this->response->json(['time' => date('H:i:s')]);
    }

    public function html(): void
    {
        $this->response->html('<h1>Hello,</h1><h2>Welcome PHPRTC Homepage.</h2>');
    }
}