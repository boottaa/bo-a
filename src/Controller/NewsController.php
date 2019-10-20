<?php

namespace App\Controller;

use App\Entity\Comments;
use App\Entity\News;
use App\Events\CommentCreatedEvent;
use App\Form\CommentType;
use App\Repository\NewsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;

/**
 * Class NewsController
 * @package App\Controller
 */
class NewsController extends AbstractController
{
    /**
     * @Route("/news/{tag}", defaults={"tag": "all", "_format"="html"}, methods={"GET"}, name="news_index")
     * @Route("/news/{tag}/rss.xml", defaults={"tag": "all", "_format"="xml"}, methods={"GET"}, name="news_rss")
     * @Cache(smaxage="10")
     *
     * @param Request $request
     * @param string $tag
     * @param string $_format
     * @param NewsRepository $news_repo
     * @return Response
     * @throws Exception
     */
    public function index(Request $request, string $tag, string $_format, NewsRepository $news_repo): Response
    {
        $tags = explode('&',  mb_strtolower($tag));

        $page = $request->get('page', 1);
        $results = $news_repo->findLatest($tags, $page);

        return $this->render('news/index.html.twig', [
            'paginator' => $results,
        ]);
    }

    /**
     * @Route("/news/view/{id}-{text}", name="news_view")
     *
     * @param News $news
     * @return Response
     */
    public function view(News $news): Response
    {
        return $this->render('news/view.html.twig', [
            'news' => $news,
        ]);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param EventDispatcherInterface $eventDispatcher
     * @param News $news
     * @return Response
     */
    public function commentForm(Request $request, EntityManagerInterface $em, EventDispatcherInterface $eventDispatcher, News $news): Response
    {
        return $this->render('news/_comment_form.html.twig', [
            'news' => $news,
            'form' => $this->createForm(CommentType::class)->createView()
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

            $eventDispatcher->dispatch(new CommentCreatedEvent($comment));

            return $this->redirectToRoute('news_view', ['id' => $news->getId(), 'text' => $news->getSlug()]);
        }

        return $this->render('news/comment_form_error.html.twig', [
            'news' => $news,
            'form' => $form->createView(),
        ]);
    }
}
