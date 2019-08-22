<?php
namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class SonataController
 *
 * @Route("/admin")
 * @IsGranted("ROLE_ADMIN")
 * @package App\Controller\Admin
 */
class SonataController extends AbstractController
{

    /**
     * @Route("/", methods={"GET"}, name="adm")
     * @Cache(smaxage="1")
     */
    public function index(Request $request): Response
    {

        return $this->render('admin/index.html.twig');
    }
}