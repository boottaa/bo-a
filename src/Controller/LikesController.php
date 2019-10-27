<?php

namespace App\Controller;

use App\Entity\News;
use App\Utils\ExpLibs\Dislike;
use App\Utils\ExpLibs\Like;
use App\Utils\Likes;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class LikesController extends AbstractController
{

    public function index()
    {
        return $this->render('likes/index.html.twig', [
            'controller_name' => 'LikesController',
        ]);
    }

    public function likesForm(News $news, Likes $likes)
    {
        $likes->setNews($news)->setUser($this->getUser());
        $rating = [
            'vote_like'    => (bool) $likes->userVoteLike(),
            'vote_dislike' => (bool) $likes->userVoteDislikes(),
        ];

        $rating['likes'] = $likes->likeCount();
        $rating['dislikes'] = $likes->dislikeCount();

        return $this->render('likes/form.html.twig', [
            'news_id' => $news->getId(),
            'rating' => $rating
        ]);
    }


    /**
     * @Route("/likes/{type}/{id}", name="news_add_like")
     */
    public function addLike(Request $request, EntityManagerInterface $em, EventDispatcherInterface $eventDispatcher, Likes $likes, string $type, News $news)
    {
        $likes->setNews($news)->setUser($this->getUser());

        $need_add = true;
        $user = $this->getUser();

        if ($dislike = $likes->userVoteDislikes()) {
            $news->removeDislike($dislike);
            $need_add = false;
        }
        if ($like = $likes->userVoteLike()) {
            $news->removeLike($like);
            $need_add = false;
        }

        if ($type === 'like' && $need_add) {
            $newObject = new \App\Entity\Likes();
            $newObject->setUser($user);
            $news->addLike($newObject);
            $em->persist($newObject);
        } elseif ($type === 'dislike' && $need_add) {
            $newObject = new \App\Entity\Dislikes();
            $newObject->setUser($user);
            $news->addDislike($newObject);
            $em->persist($newObject);
        }

        $em->flush();

        return new JsonResponse([
            'success' => true
        ]);
    }
}
