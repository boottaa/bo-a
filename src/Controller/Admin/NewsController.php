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
 * @Route("/admin/news", name="adm_news")
 * @IsGranted("ROLE_REDACTOR")
 * @package App\Controller\Admin
 */
class NewsController extends AbstractController
{
    /**
     * @Route("/add", name="adm_news_add")
     * @Cache(smaxage="1")
     */
    public function index(Request $request, EntityManagerInterface $em): Response
    {

        $news = new News();
        $news->setAuthor($this->getUser());

        $form = $this->createForm(AddEditNewsType::class, $news);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            
//            $news->addTag();
            
            $em->persist($news);
            $em->flush();
            return $this->redirectToRoute('index');
        }
        return $this->render('admin/form.html.twig', [
            'controller_name' => 'IndexController',
            'form' => $form->createView(),
        ]);

    }
}
