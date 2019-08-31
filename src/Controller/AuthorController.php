<?php

namespace App\Controller;

use App\Entity\Users;
use App\Repository\NewsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/author")
 */
class AuthorController extends AbstractController
{
    /**
     * @Route("/{id<\d+>}", name="author")
     */
    public function index(Users $author, NewsRepository $news)
    {
        /**
         * @var Users $user
         */
        $user = $this->getUser();
        $btn_subscribed = false;
        if (
            $this->isGranted('ROLE_USER') &&
            isset($user) &&
            $user->getId() != $author->getId() &&
            $user->isFollowed($author) === false
        ) {
            $btn_subscribed = true;
        }


        $paginator = $news->findLatestAuthor(1, $author);

        return $this->render('author/index.html.twig', [
            'author' => $author,
            'btn_subscribed' => $btn_subscribed,
            'paginator' => $paginator,
        ]);
    }

    /**
     * @Route("/subscribed/{id<\d+>}", name="subscribed_to_author")
     * @IsGranted("ROLE_USER")
     */
    public function subscribed(Users $author, EntityManagerInterface $em)
    {
        /**
         * @var Users $user
         */
        $user = $this->getUser();

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
