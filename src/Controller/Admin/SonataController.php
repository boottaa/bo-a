<?php
namespace App\Controller\Admin;

use App\Entity\Users;
use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class SonataController
 *
 * @Route("/admin")
 * @IsGranted("ROLE_USER")
 * @package App\Controller\Admin
 */
class SonataController extends AbstractController
{

    /**
     * @Route("/", methods={"GET"}, name="adm_index")
     * @Cache(smaxage="1")
     */
    public function index(Request $request, UsersRepository $users): Response
    {
        $ref_link = $this->generateUrl('ref', ['ref_hash' => $this->getUser()->getRefHash()], UrlGeneratorInterface::ABSOLUTE_URL);
        $referals = $users->findBy(['invited' => $this->getUser()->getId()]);

        return $this->render(
            'admin/index.html.twig',
            [
                'ref_link' => $ref_link,
                'ref_count' => count($referals),
            ]
        );
    }
}