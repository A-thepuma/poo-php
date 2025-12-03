<?php

declare(strict_types=1);

// L'espace de noms doit correspondre EXACTEMENT à celui de l'erreur
namespace App\Domain\MatchMaker; 

// Toutes les exceptions personnalisées doivent hériter d'une classe Exception de base
class PlayerNotFoundInLobbyException extends \RuntimeException
{
    // Le corps de la classe est souvent vide si l'on ne fait qu'encapsuler la fonctionnalité de base
}