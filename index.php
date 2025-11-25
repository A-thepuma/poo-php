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
        $this->queuingPlayers[] = new QueuingPlayer($player->getName(), $player->getRatio());
    }

    public function addPlayers(Player ...$players)
    {
        foreach ($players as $player) {
            $this->addPlayer($player);
        }
    }
}

abstract class Player extends User
{
    protected string $name;
    protected float $ratio;

    public function __construct(string $name, float $ratio = 400.0)
    {
        $this->name = $name;
        $this->ratio = $ratio;
    }

    public function getName(): string
    {
        return $this->name;
    }

    private function probabilityAgainst(self $player): float
    {
        return 1 / (1 + (10 ** (($player->getRatio() - $this->getRatio()) / 400)));
    }

    public function updateRatioAgainst(self $player, int $result): void
    {
        $this->ratio += 32 * ($result - $this->probabilityAgainst($player));
    }

    public function getRatio(): float
    {
        return $this->ratio;
    }

}

class QueuingPlayer extends Player
{
    private int $range = 1;

    public function __construct(string $name, float $ratio = 400.0)
    {
        parent::__construct($name, $ratio);
    }

    public function getRange(): int
    {
        return $this->range;
    }

    public function setRange(int $range): void
    {
        $this->range = $range;
    }

    public function getUsername(): string
    {
        return $this->getName();
    }
}


abstract class User {
    public const STATUS_ACTIVE = 'active'; public const STATUS_INACTIVE = 'inactive'; 
    public function __construct(public string $email, public string $status = self::STATUS_ACTIVE) {

     } 
     public function setStatus(string $status): void { 
        assert( 
            in_array($status, [self::STATUS_ACTIVE, self::STATUS_INACTIVE]), 
            sprintf( 
                'Le status %s n\'est pas valide. Les status possibles sont : %s', 
                $status, [self::STATUS_ACTIVE, self::STATUS_INACTIVE]) 
            ); 
        $this->status = $status; 
    } 
    public function getStatus(): string 
    { 
        return $this->status;
    }

    abstract public function getUsername():string;

}

final class Admin extends User
{
    public array $roles;

    public function __construct(string $email, string $status = self::STATUS_ACTIVE, array $roles = [])
    {
        parent::__construct($email, $status);
        $this->roles = $roles;
    }

    public function getUsername(): string
    {
        return $this->email; 
    }
}


$admin = new Admin('trompete@guy.com', 'Ibrahim Maalouf');
var_dump($admin);

$greg = new QueuingPlayer('greg', 400);
$jade = new QueuingPlayer('jade', 476);


$lobby = new Lobby();
$lobby->addPlayers($greg, $jade);

var_dump($lobby->findOponents($lobby->queuingPlayers[0]));

exit(0);
