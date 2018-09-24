<?php

namespace App\TicTacToe;

class GameFullException extends \Exception
{
}

class PlayersMustBeDifferentException extends \Exception
{
}

class GameNotStartedException extends \Exception
{
}

class GameFinishedException extends \Exception
{
}

class SpaceNotEmptyException extends \Exception
{
}

class Game
{
    public $id;

    public $player1;
    public $player2;

    public $turn;

    public $board;
    public function __construct()
    {
        $this->id = uniqid();
        $this->board = [
            [null, null, null],
            [null, null, null],
            [null, null, null]
        ];
    }

    private function randomlyChooseStartingPlayer()
    {
        $this->turn = rand(0, 1) == 0 ? $this->player1 : $this->player2;
    }

    public function join(string $playerId): void
    {
        if ($this->player1 == null) {
            $this->player1 = $playerId;
        } elseif ($this->player2 == null) {
            if ($this->player1 == $playerId) {
                throw new PlayersMustBeDifferentException();
            }
            $this->player2 = $playerId;
            $this->randomlyChooseStartingPlayer();
        } else {
            throw new GameFullException();
        }
    }

    public function hasHorizontallyAlignedRow(string $playerId): bool
    {
        foreach ($this->board as $row) {
            $scores = array_count_values(array_filter($row));
            if (
                array_key_exists($playerId, $scores) &&
                $scores[$playerId] == 3
            ) {
                return true;
            }
        }
        return false;
    }

    public function hasVerticallyAlignedRow(string $playerId): bool
    {
        for ($i = 0; $i <= 2; $i++) {
            $column = [
                $this->board[0][$i],
                $this->board[1][$i],
                $this->board[2][$i]
            ];
            $scores = array_count_values(array_filter($column));
            if (
                array_key_exists($playerId, $scores) &&
                $scores[$playerId] == 3
            ) {
                return true;
            }
        }
        return false;
    }

    public function hasDiagonalsAligned(string $playerId): bool
    {
        $diagonals = [
            [$this->board[0][0], $this->board[1][1], $this->board[2][2]],
            [$this->board[0][2], $this->board[1][1], $this->board[2][0]]
        ];
        foreach ($diagonals as $diagonal) {
            $scores = array_count_values(array_filter($diagonal));
            if (
                array_key_exists($playerId, $scores) &&
                $scores[$playerId] == 3
            ) {
                return true;
            }
        }
        return false;
    }

    public function getWinner()
    {
        foreach ([$this->player1, $this->player2] as $player) {
            if ($this->hasHorizontallyAlignedRow($player)) {
                return $player;
            }
            if ($this->hasVerticallyAlignedRow($player)) {
                return $player;
            }
            if ($this->hasDiagonalsAligned($player)) {
                return $player;
            }
        }
        return null;
    }

    private function isRowFull($row): bool
    {
        return count(array_filter($row)) === 3;
    }

    public function isBoardFull(): bool
    {
        return $this->isRowFull($this->board[0]) &&
            $this->isRowFull($this->board[1]) &&
            $this->isRowFull($this->board[2]);
    }

    public function isStarted(): bool
    {
        return $this->player1 != null && $this->player2 != null;
    }

    public function play(string $playerId, int $x, int $y): void
    {
        if (!$this->isStarted()) {
            throw new GameNotStartedException();
        }
        if ($this->getWinner() != null || $this->isBoardFull()) {
            throw new GameFinishedException();
        }
        if ($x < 0 || $x > 2 || $y < 0 || $y > 2) {
            throw new \OutOfRangeException();
        }
        if ($this->board[$x][$y] != null) {
            throw new SpaceNotEmptyException();
        }
        $this->board[$x][$y] = $playerId;
        $this->turn =
            $this->turn == $this->player1 ? $this->player2 : $this->player1;
    }
}
