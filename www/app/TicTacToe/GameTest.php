<?php

use PHPUnit\Framework\TestCase;

use App\TicTacToe\Game;
use App\TicTacToe\GameNotStartedException;
use App\TicTacToe\GameFullException;
use App\TicTacToe\PlayersMustBeDifferentException;
use App\TicTacToe\GameFinishedException;
use App\TicTacToe\SpaceNotEmptyException;

final class GameTest extends TestCase
{
    private function initializeGame(): Game
    {
        $game = new Game();

        $player1 = uniqid();
        $player2 = uniqid();

        $game->join($player1);
        $game->join($player2);

        return $game;
    }

    public function testTwoPlayersCanJoinTheGame()
    {
        $game = new Game();

        $player1 = uniqid();
        $game->join($player1);
        $this->assertEquals($game->player1, $player1);

        $player2 = uniqid();
        $game->join($player2);
        $this->assertEquals($game->player2, $player2);
    }

    public function testTheTwoPlayersMustBeDifferent()
    {
        $this->expectException(PlayersMustBeDifferentException::class);

        $game = new Game();

        $player1 = uniqid();
        $game->join($player1);
        $game->join($player1);
    }

    public function testThereCanBeOnlyTwoPlayers()
    {
        $this->expectException(GameFullException::class);

        $game = $this->initializeGame();

        $player3 = uniqid();
        $game->join($player3);
    }

    public function testICannotPlayOutsideTheBoard()
    {
        $this->expectException(OutOfRangeException::class);

        $game = $this->initializeGame();

        $game->play($game->player1, 1, 17);
    }

    public function testICanOnlyPlayWhenBothPlayersJoined()
    {
        $this->expectException(GameNotStartedException::class);

        $game = new Game();
        $player1 = uniqid();
        $game->join($player1);

        $game->play($player1, 1, 17);
    }

    public function testItPlayedWhereIAskedToPlay()
    {
        $game = $this->initializeGame();

        $game->play($game->player1, 1, 1);

        $this->assertEquals($game->board, [
            [null, null, null],
            [null, $game->player1, null],
            [null, null, null]
        ]);

        $game->play($game->player2, 0, 1);

        $this->assertEquals($game->board, [
            [null, $game->player2, null],
            [null, $game->player1, null],
            [null, null, null]
        ]);
    }

    public function testItDetectsWinnerOnHorizontalRow()
    {
        $game = $this->initializeGame();
        $game->board = [
            [null, null, null],
            [$game->player1, $game->player1, $game->player1],
            [null, null, null]
        ];
        $this->assertEquals($game->getWinner(), $game->player1);
    }

    public function testItDetectsWinnerOnVerticalRow()
    {
        $game = $this->initializeGame();
        $game->board = [
            [null, null, $game->player1],
            [null, null, $game->player1],
            [null, null, $game->player1]
        ];
        $this->assertEquals($game->getWinner(), $game->player1);
    }

    public function testItDetectsWinnerOnDiagonals()
    {
        $game = $this->initializeGame();
        $game->board = [
            [null, null, $game->player1],
            [null, $game->player1, null],
            [$game->player1, null, null]
        ];
        $this->assertEquals($game->getWinner(), $game->player1);
    }

    public function testICannotPlayWhenSomeoneWon()
    {
        $this->expectException(GameFinishedException::class);
        $game = $this->initializeGame();
        $game->board = [
            [null, null, $game->player1],
            [null, $game->player1, null],
            [$game->player1, null, null]
        ];

        $game->play($game->player1, 1, 2);

        $this->assertEquals($game->board, [
            [null, null, $game->player1],
            [null, $game->player1, null],
            [$game->player1, null, null]
        ]);
    }

    public function testICannotPlayWhenItsADraw()
    {
        $this->expectException(GameFinishedException::class);
        $game = $this->initializeGame();
        $game->board = [
            [$game->player2, $game->player1, $game->player2],
            [$game->player1, $game->player1, $game->player2],
            [$game->player1, $game->player2, $game->player1]
        ];
        $game->play($game->player1, 1, 2);
    }

    public function testICannotPlayWhenBoardIsFullAndSomeoneWon()
    {
        $this->expectException(GameFinishedException::class);
        $game = $this->initializeGame();
        $game->board = [
            [$game->player2, $game->player2, $game->player2],
            [$game->player1, $game->player1, $game->player2],
            [$game->player1, $game->player2, $game->player1]
        ];
        $game->play($game->player1, 1, 2);
    }

    public function testItInitializesTheFirstPlayer()
    {
        $game = $this->initializeGame();

        $this->assertContains($game->turn, [$game->player1, $game->player2]);
    }

    public function testItSwitchesTurnsWhenPlaying()
    {
        $game = $this->initializeGame();
        $startingPlayer = $game->turn;
        $game->play($game->player1, 1, 2);
        $this->assertNotEquals($startingPlayer, $game->turn);
    }

    public function testICanOnlyPlayOnAnEmptySpace()
    {
        $game = $this->initializeGame();
        $game->board = [
            [null, null, null],
            [null, $game->player1, null],
            [$game->player1, null, null]
        ];
        $this->expectException(SpaceNotEmptyException::class);
        $game->play($game->player2, 1, 1);
    }
}
