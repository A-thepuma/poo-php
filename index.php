<?php

/*
 * This file is part of the OpenClassRoom PHP Object Course.
 *
 * (c) Grégoire Hébert <contact@gheb.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// declare(strict_types=1);

class Lobby
{
    /** @var array<QueuingPlayer> */
    public  $queuingPlayers = [];

    public function findOponents(QueuingPlayer $player)
    {
        $minLevel = round($player->getRatio() / 100);
        $maxLevel = $minLevel + $player->getRange();

        return array_filter($this->queuingPlayers, static function (QueuingPlayer $potentialOponent) use ($minLevel, $maxLevel, $player) {
            $playerLevel = round($potentialOponent->getRatio() / 100);

            return $player !== $potentialOponent && ($minLevel <= $playerLevel) && ($playerLevel <= $maxLevel);
        });
    }

    public function addPlayer(Player $player)
    {
        $this->queuingPlayers[] = new QueuingPlayer($player);
    }

    public function addPlayers(Player ...$players)
    {
        foreach ($players as $player) {
            $this->addPlayer($player);
        }
    }
}

class Player
{
    protected $name;
    protected $ratio;
    public function __construct( $name, $ratio = 400.0)
    {
        $this->name = $name;
        $this->ratio = $ratio;
    }

    public function getName()
    {
        return $this->name;
    }

    private function probabilityAgainst(self $player)
    {
        return 1 / (1 + (10 ** (($player->getRatio() - $this->getRatio()) / 400)));
    }

    public function updateRatioAgainst(self $player, int $result)
    {
        $this->ratio += 32 * ($result - $this->probabilityAgainst($player));
    }

    public function getRatio()
    {
        return $this->ratio;
    }
}

class QueuingPlayer extends Player
{
    private $range = 1;

    public function __construct(Player $player)
    {
        parent::__construct($player->getName(), $player->getRatio());
    }

    public function getRange()
    {
        return $this->range;
    }

    public function setRange(int $range)
    {
        $this->range = $range;
    }
}

$greg = new Player('greg', 400);
$jade = new Player('jade', 476);

$lobby = new Lobby();
$lobby->addPlayers($greg, $jade);

var_dump($lobby->findOponents($lobby->queuingPlayers[0]));

exit(0);
