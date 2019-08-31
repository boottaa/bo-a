<?php

namespace App\Controller;

use App\Entity\Comments;
use App\Entity\Dislikes;
use App\Entity\Likes;
use App\Entity\News;
use App\Events\CommentCreatedEvent;
use App\Form\CommentType;
use App\Repository\NewsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;

class NewsController extends AbstractController
{
    /**
     * @Route("/news", defaults={"page": "1", "_format"="html"}, methods={"GET"}, name="news_index")
     * @Route("/news/rss.xml", defaults={"page": "1", "_format"="xml"}, methods={"GET"}, name="news_rss")
     * @Route("/news/page/{page<[1-9]\d*>}", defaults={"_format"="html"}, methods={"GET"}, name="news_index_paginated")
     * @Cache(smaxage="10")
     */
    public function index(Request $request, int $page, string $_format, NewsRepository $news_repo)
    {
        $results = $news_repo->findLatest($page);

        return $this->render('news/index.html.twig', [
            'paginator' => $results,
        ]);
    }

    /**
     * @Route("/news/{id}", name="news_view")
     */
    public function view(News $news){

        $rating = [
            'vote_like'    => false,
            'vote_dislike' => false,
        ];
        foreach ($news->getLikes() as $like) {
            if ($like->getUser() === $this->getUser()) {
                $rating['vote_like'] = true;
            }
        }

        foreach ($news->getDislikes() as $dislike) {
            if ($dislike->getUser() === $this->getUser()) {
                $rating['vote_dislike'] = true;
            }
        }

        $rating['likes'] = $news->getLikes()->count();
        $rating['dislikes'] = $news->getDislikes()->count();
        
        return $this->render('news/view.html.twig', [
            'news' => $news,
            'rating' => $rating,
        ]);
    }

    public function commentForm(Request $request, EntityManagerInterface $em, EventDispatcherInterface $eventDispatcher, News $news)
    {
        $form = $this->createForm(CommentType::class);

        return $this->render('news/_comment_form.html.twig', [
            'news' => $news,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/news/add/comment/{id}", name="news_add_comment")
     */
    public function addComment(Request $request, EntityManagerInterface $em, EventDispatcherInterface $eventDispatcher, News $news)
    {
        $comment = new Comments();
        $comment->setAuthor($this->getUser());
        $news->addComment($comment);

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($comment);
            $em->flush();
            // See https://symfony.com/doc/current/components/event_dispatcher.html
            $eventDispatcher->dispatch(new CommentCreatedEvent($comment));

            return $this->redirectToRoute('news_view', ['id' => $news->getId()]);
        }

        return $this->render('news/comment_form_error.html.twig', [
            'news' => $news,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/news/add/like/{id}", name="news_add_like")
     */
    public function addLike(Request $request, EntityManagerInterface $em, EventDispatcherInterface $eventDispatcher, News $news)
    {
        $is_vote = false;
        foreach ($news->getLikes() as $like) {
            if ($like->getUser() === $this->getUser()) {
                $is_vote = true;
            }
        }

        foreach ($news->getDislikes() as $dislike) {
            if ($dislike->getUser() === $this->getUser()) {
                $is_vote = true;
            }
        }

        if ($is_vote) {
            $news->removeLike($news->getLikes()->current());
        } else {
            $like = new Likes();
            $like->setUser($this->getUser());
            $news->addLike($like);
            $em->persist($like);
        }

        $em->flush();
        
        return $this->redirectToRoute('news_view', ['id' => $news->getId()]);
    }

    /**
     * @Route("/news/add/dislike/{id}", name="news_add_dislike")
     */
    public function addDislike(Request $request, EntityManagerInterface $em, EventDispatcherInterface $eventDispatcher, News $news)
    {
        $is_vote = false;
        foreach ($news->getLikes() as $like) {
            if ($like->getUser() === $this->getUser()) {
                $is_vote = true;
            }
        }

        foreach ($news->getDislikes() as $dislike) {
            if ($dislike->getUser() === $this->getUser()) {
                $is_vote = true;
            }
        }

        if ($is_vote) {
            $news->removeDislike($news->getDislikes()->current());
        } else {
            $dislike = new Dislikes();
            $dislike->setUser($this->getUser());
            $news->addDislike($dislike);
            $em->persist($dislike);
        }
        $em->flush();

        return $this->redirectToRoute('news_view', ['id' => $news->getId()]);
    }

}
