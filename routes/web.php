<?php

use App\Lifewire;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/lifewire', function() {
    $lifewire = new Lifewire();
    $snapshot = request('snapshot');
    $component = $lifewire->fromSnapshot($snapshot);

    if ($action = request('action')) {
        $lifewire->call($component, $action);
    }

    if ([$property, $value] = request('update')) {
        $lifewire->setProperty($component, $property, $value);
    }

    [$html, $snapshot] = $lifewire->toSnapshot($component);

    return [
        'html' => $html,
        'snapshot' => $snapshot,
    ];
});

Blade::directive('lifewire', function ($expression) {
    return "<?php echo app('App\Lifewire')->initialRender($expression); ?>";
});
