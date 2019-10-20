<?php

namespace App\Controller\Admin;

use App\Repository\LikesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
     *
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param LikesRepository $likes
     * @return Response
     */
    public function index(Request $request, EntityManagerInterface $em, LikesRepository $likes): Response
    {
        return $this->render('admin/likes/index.html.twig', [
            'paginator' => $likes->findLatest($this->getUser()->getId())
        ]);
    }

}
