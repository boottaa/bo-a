<?php

namespace App\Utils;

use App\Entity\Users;

/**
 * Количества опыта
 *
 * Class Points
 *
 * @package App\Utils
 */
class Exp
{
    private $level = [
        1 => 1000,
        2 => 5000,
        3 => 15000,
        4 => 30000,
        5 => 60000,
        6 => 120000,
        7 => 240000,
        8 => 580000,
        9 => 1160000,
        10 => 2320000
    ];

    /**
     * @param Users $user
     * @return array
     */
    public function getLevel(Users $user): array
    {
        foreach ($this->level as $level => $exp) {
            if ($user->getExp() < $exp) {
                return [$level - 1, $this->level[$level]];
            }
        }
    }
}