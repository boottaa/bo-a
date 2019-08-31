<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class LikesController extends AbstractController
{
    /**
     * @Route("/likes", name="likes")
     */
    public function index()
    {
        return $this->render('likes/index.html.twig', [
            'controller_name' => 'LikesController',
        ]);
    }
}
