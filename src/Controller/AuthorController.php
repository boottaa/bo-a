<?php

namespace App\Controller;

use App\Entity\Users;
use App\Repository\NewsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AuthorController extends AbstractController
{
    /**
     * @Route("/author/{id<\d+>}", name="author")
     */
    public function index(Users $author, NewsRepository $news)
    {
        $paginator = $news->findLatestAuthor(1, $author);

        return $this->render('author/index.html.twig', [
            'author' => $author,
            'paginator' => $paginator,
        ]);
    }
}
