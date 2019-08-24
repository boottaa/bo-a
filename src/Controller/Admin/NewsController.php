<?php

namespace App\Controller\Admin;

use App\Entity\News;
use App\Form\AddEditNewsType;
use App\Repository\NewsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class NewsController
 *
 * @Route("/admin/news")
 * @IsGranted("ROLE_REDACTOR")
 * @package App\Controller\Admin
 */
class NewsController extends AbstractController
{

    /**
     * @Route("/", name="adm_news")
     * @Cache(smaxage="1")
     */
    public function index(Request $request, EntityManagerInterface $em, NewsRepository $news)
    {
        return $this->render('admin/news/index.html.twig', [
            'list' => $news->findBy(['author' => $this->getUser()], ['created_at' => 'DESC'])
        ]);
    }

    /**
     * @Route("/add", name="adm_news_add")
     * @Route("/edit/{id<\d+>}", name="adm_news_edit")
     * @Cache(smaxage="1")
     */
    public function add(Request $request, EntityManagerInterface $em, News $news = null): Response
    {
        if (empty($news)) {
            $news = new News();
            $news->setAuthor($this->getUser());
        }

        $form = $this->createForm(AddEditNewsType::class, $news);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

//            $news->addTag();

            $em->persist($news);
            $em->flush();
            return $this->redirectToRoute('adm_news');
        }
        return $this->render('admin/form.html.twig', [
            'controller_name' => 'IndexController',
            'form' => $form->createView(),
        ]);
    }
}
