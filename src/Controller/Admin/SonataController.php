<?php
namespace App\Controller\Admin;

use App\Entity\Users;
use App\Form\RegistrationType;
use App\Form\Type\TagsInputType;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;

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
        /**
         * @var Users $user;
         */
        $user = $this->getUser();

        $ref_link = $this->generateUrl('ref', ['ref_hash' => $user->getRefHash()], UrlGeneratorInterface::ABSOLUTE_URL);
        $referals = $users->findBy(['invited' => $user->getId()]);

        return $this->render(
            'admin/index.html.twig',
            [
                'ref_link' => $ref_link,
                'ref_count' => count($referals),
                'followers' => count($user->getFollowers()),
            ]
        );
    }

    /**
     * @Route("/edit_profile", methods={"GET", "POST"}, name="adm_edit_profile")
     * @Cache(smaxage="1")
     */
    public function editProfile(Request $request, EntityManagerInterface $em)
    {
        if (!$this->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('index');
        }

        /**
         * @var Users $user
         */
        $user = $this->getUser();
        $form = $this
            ->createForm(RegistrationType::class, $user)
            ->add('hobbies', TagsInputType::class, [
                'label' => 'Увлечения:',
                'help' => 'Укажите не более 6 ваших увлечений и интересов'
            ])->remove('password');

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('index');
        }
        return $this->render('admin/form.html.twig', [
            'controller_name' => 'IndexController',
            'form' => $form->createView(),
        ]);
    }

}