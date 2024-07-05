<?php

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Blade::directive('lifewire', function ($expression) {
    return "<?php echo app('App\Lifewire')->initialRender($expression); ?>";
});
