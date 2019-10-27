<?php

namespace App\Controller;

use App\Entity\Users;
use App\Repository\NewsRepository;
use App\Utils\ExpLibs\Subscribe;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/author")
 */
class AuthorController extends AbstractController
{
    /**
     * @Route("/{id<\d+>}", name="author")
     *
     * @param Request $request
     * @param Users $author
     * @param NewsRepository $news
     * @return Response
     * @throws Exception
     */
    public function index(Request $request, Users $author, NewsRepository $news): Response
    {
        /**
         * @var Users $user
         */
        $user = $this->getUser();
        $page = $request->get('page', 1);

        $btn_subscribe = [];
        if ($this->isGranted('ROLE_USER') && isset($user) && $user->getId() != $author->getId()) {

            if ($user->isFollowed($author) === false) {
                $btn_subscribe = [
                    'title' => 'Подписаться',
                    'path' => $this->generateUrl('subscribed_to_author', ['id' => $author->getId()])
                ];
            } elseif ($user->isFollowed($author) === true) {
                $btn_subscribe = [
                    'title' => 'Отписаться',
                    'path' => $this->generateUrl('unsubscribe_to_author', ['id' => $author->getId()])
                ];
            }
        }
        $paginator = $news->findLatestAuthor($author, $page);

        return $this->render('author/index.html.twig', [
            'author' => $author,
            'btnSubscribe' => $btn_subscribe,
            'paginator' => $paginator
        ]);
    }

    /**
     * @Route("/subscribed/{id<\d+>}", name="subscribed_to_author")
     * @IsGranted("ROLE_USER")
     */
    public function subscribed(Users $author, EntityManagerInterface $em, Subscribe $subscribe)
    {
        /**
         * @var Users $user
         */
        $user = $this->getUser();

        $subscribe->add($author, $user);
        $user->addFolloweTo($author);
        $em->persist($user);
        $em->flush();

        return $this->redirectToRoute('author', ['id' => $author->getId()]);
    }

    /**
     * @Route("/unsubscribe/{id<\d+>}", name="unsubscribe_to_author")
     * @IsGranted("ROLE_USER")
     */
    public function unsubscribe(Users $author, EntityManagerInterface $em)
    {
        /**
         * @var Users $user
         */
        $user = $this->getUser();

        $user->removeFolloweTo($author);
        $em->persist($user);
        $em->flush();

        return $this->redirectToRoute('author', ['id' => $author->getId()]);
    }
}
