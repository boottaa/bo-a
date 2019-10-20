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

    public const
        ACTION_ADD_NEWS = 1,
        ACTION_SUBSCRIBE = 2,
        ACTION_LIKE = 3,
        ACTION_DISLIKE = 4,
        ACTION_REFERAL = 5;

    public const
        ADD_NEWS = 200, //Добавил новость
        U_SUBSCRIBE = 10, //Подписался
        SUBSCRIBE = 100, //Получил подписчика
        U_LIKE = 5, //Поставил лайк
        LIKE = 10, //Получил лайк
        U_DISLIKE = 5, //Поставил дизлайк
        DISLIKE = 5, //Получил дизлайк
        NEW_REFERAL = 500, //Привел реферала
        U_REFERAL = 200; //За регистрацию по реферальной ссылки

    /**
     * @param int $action
     * @param Users $user
     * @param Users|null $author
     */
    public function addExp(int $action, Users $user, ?Users $author = null): void
    {
        if($user === $author){
            return;
        }

        switch ($action){
            case self::ACTION_ADD_NEWS:
                $user->addExpa(self::ADD_NEWS);
                break;
            case self::ACTION_SUBSCRIBE:
                $user->addExpa(self::U_SUBSCRIBE);
                $author->addExpa(self::SUBSCRIBE);
                break;
            case self::ACTION_LIKE:
                $user->addExpa(self::U_LIKE);
                $author->addExpa(self::LIKE);
                break;
            case self::ACTION_DISLIKE:
                $user->addExpa(self::U_DISLIKE);
                $author->addExpa(self::DISLIKE);
                break;
            case self::ACTION_REFERAL:
                $user->addExpa(self::U_REFERAL);
                $author->addExpa(self::NEW_REFERAL);
                break;
        }
    }

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