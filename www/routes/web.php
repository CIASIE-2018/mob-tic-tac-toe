<?php

use App\TicTacToe\Game;

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

Route::get('/', function () {
    $game = cache('game');
    $me = Cookie::get('id');
    if (!$me) {
        $me = uniqid();
        Cookie::queue('id', $me, 60 * 5);
    }
    if (!$game) {
        $game = new Game();
    }
    if (!$game->isStarted()) {
        $game->join($me);
    }
    if (
        $game->isStarted() &&
        !in_array($me, [$game->player1, $game->player2])
    ) {
        abort(404);
    }
    cache(
        [
            'game' => $game
        ],
        60 * 5
    );
    return view('welcome', [
        'game' => $game
    ]);
});

Route::get('/reset', function () {
    cache(
        [
            'game' => null
        ],
        60 * 5
    );

    return redirect('/');
});

Route::get('/play/{x}/{y}', function (Request $request, $x, $y) {
    $game = cache('game');
    $me = Cookie::get('id');
    print_r($game);
    if ($game->isStarted() && $game->turn == $me) {
        $game->play($game->turn, $x, $y);
        cache(
            [
                'game' => $game
            ],
            60 * 5
        );
    }
    return redirect('/');
})->name('play');
