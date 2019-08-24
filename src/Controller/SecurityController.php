<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\RegistrationType;
use App\Security\Role;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class SecurityController extends AbstractController
{
    use TargetPathTrait;

    /**
     * @Route("/login", name="security_login")
     * @param Request             $request
     * @param Security            $security
     * @param AuthenticationUtils $helper
     *
     * @return Response
     */
    public function login(Request $request, Security $security, AuthenticationUtils $helper): Response
    {
        if ($security->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('index');
        }

        $this->saveTargetPath($request->getSession(), 'main', $this->generateUrl('index'));

        return $this->render('security/login.html.twig', [
            'last_username' => $helper->getLastUsername(),
            'error' => $helper->getLastAuthenticationError(),
        ]);
    }

    /**
     * @Route("/registration", name="security_registration")
     * @param Request                      $request
     * @param EntityManagerInterface       $em
     * @param UserPasswordEncoderInterface $encoder
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function registration(Request $request, Security $security, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder)
    {
        if ($security->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('index');
        }

        $users = new Users();
        $form = $this->createForm(RegistrationType::class, $users);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $invided = $_COOKIE['ref_id'] ?? 0;
            $users
                ->setPassword($encoder->encodePassword($users, $form->get('password')->getData()))
                ->setInvited($invided)
                ->setRefHash(md5($form->get('email')->getData() . $form->get('login')->getData()))
            ;

            $em->persist($users);
            $em->flush();
            return $this->redirectToRoute('index');
        }
        return $this->render('security/registration.html.twig', [
            'controller_name' => 'IndexController',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/ref/{ref_hash<\w+>}", methods={"GET"}, name="ref")
     */
    public function ref(Request $request, Security $security, Users $user): Response
    {
        if ($security->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('adm_index');
        }

        setcookie('ref_id', $user->getId(), time() + 60 * 60 * 24 * 30, '/');
        return $this->redirectToRoute('security_registration', [], 301);
    }

    /**
     * @Route("/logout", name="security_logout")
     */
    public function logout(): void
    {
        throw new \Exception('This should never be reached!');
    }
}
