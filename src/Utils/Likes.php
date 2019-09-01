<?php

namespace App\Utils;

use App\Entity\News;
use App\Entity\Users;

/**
 * Class VarExportArray
 * @package App\Utils
 */
class Likes
{
    /**
     * @var News $news
     */
    private $news;

    /**
     * @var Users $user
     */
    private $user;

    function setUser(Users $user)
    {
        $this->user = $user;

        return $this;
    }

    function setNews(News $news)
    {
        $this->news = $news;

        return $this;
    }

    function likeCount()
    {
        return $this->news->getLikes()->count();
    }

    function dislikeCount()
    {
        return $this->news->getDislikes()->count();
    }

    function userVoteLike()
    {
        foreach ($this->news->getLikes() as $like) {
            if ($like->getUser() === $this->user) {
                return $like;
            }
        }
        return false;
    }

    function userVoteDislikes()
    {
        foreach ($this->news->getDislikes() as $dislike) {
            if ($dislike->getUser() === $this->user) {
                return $dislike;
            }
        }
        return false;
    }
}