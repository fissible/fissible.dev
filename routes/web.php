<?php

use App\Http\Controllers\MarketingController;
use Illuminate\Support\Facades\Route;

// Marketing pages
Route::get('/', [MarketingController::class, 'home']);
Route::get('/station', [MarketingController::class, 'station']);
Route::view('/station/api-pro', 'pages.station.api-pro');
Route::get('/tools', [MarketingController::class, 'toolsIndex']);
Route::get('/tools/{slug}', [MarketingController::class, 'toolShow']);

// Products — Fissible Phone lives on its own domain; redirect the legacy page
Route::redirect('/phone', 'https://fissiblephone.com', 301);

// Case studies
Route::view('/case-studies', 'pages.case-studies');

// Legal
Route::view('/privacy', 'pages.privacy');

// SMS moved to the Mesabit site (mesabit.net) — redirect legacy URLs
Route::redirect('/sms-terms', 'https://mesabit.net/sms-terms', 301);
Route::redirect('/text', 'https://mesabit.net/text', 301);

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

