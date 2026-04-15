<?php

use App\Http\Controllers\MarketingController;
use Illuminate\Support\Facades\Route;

// Marketing pages
Route::get('/', [MarketingController::class, 'home']);
Route::get('/station', [MarketingController::class, 'station']);
Route::get('/tools', [MarketingController::class, 'toolsIndex']);
Route::get('/tools/{slug}', [MarketingController::class, 'toolShow']);

// Coming soon products
Route::get('/guit', [MarketingController::class, 'comingSoon'])
    ->defaults('slug', 'guit');
Route::get('/sigil', [MarketingController::class, 'comingSoon'])
    ->defaults('slug', 'sigil');
Route::get('/conduit', [MarketingController::class, 'comingSoon'])
    ->defaults('slug', 'conduit');

// Redirects — only for tools that have pages
Route::permanentRedirect('/accord', '/tools/accord');
Route::permanentRedirect('/drift', '/tools/drift');
Route::permanentRedirect('/forge', '/tools/forge');
Route::permanentRedirect('/seed', '/tools/seed');
Route::permanentRedirect('/shellframe', '/tools/shellframe');
Route::permanentRedirect('/ptyunit', '/tools/ptyunit');
Route::permanentRedirect('/shellql', '/tools/shellql');

// Deprecated modules — redirect to tools index
Route::permanentRedirect('/watch', '/tools');
Route::permanentRedirect('/fault', '/tools');
