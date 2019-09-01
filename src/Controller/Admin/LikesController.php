<?php

namespace App\Controller\Admin;


use App\Entity\Users;
use App\Repository\LikesRepository;
use App\Repository\NewsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class LikesController
 * Избранное
 *
 * @Route("/admin/likes")
 * @IsGranted("ROLE_USER")
 * @package App\Controller\Admin
 */
class LikesController extends AbstractController
{
    /**
     * @Route("/", name="adm_likes")
     * @Cache(smaxage="1")
     */
    public function index(Request $request, EntityManagerInterface $em, LikesRepository $likes)
    {
//        dd($likes->findLatest($this->getUser()->getId()));

        return $this->render('admin/likes/index.html.twig', [
            'paginator' => $likes->findLatest($this->getUser()->getId())
        ]);
    }

}
