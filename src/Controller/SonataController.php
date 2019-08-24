<?php
namespace App\Controller;

use App\Entity\Users;
use App\Security\Role;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;

class SonataController extends AbstractController
{

    /**
     * @Route("/", methods={"GET"}, name="index")
     * @Cache(smaxage="1")
     */
    public function index(Request $request): Response
    {
        return $this->render('sonata/index.html.twig');
    }

}