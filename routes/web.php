<?php

declare(strict_types=1);

use App\Actions\SpotifyAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Home');
});

Route::get('/spotify/authorize', function (SpotifyAuth $spotifyAuth) {
    return redirect($spotifyAuth->getAuthorizationUrl());
});

Route::get('/spotify/callback', function (Request $request, SpotifyAuth $spotifyAuth) {
    $spotifyAuth->exchangeCode($request->query('code'));

    return redirect('prezes-bot://spotify-connected');
});
