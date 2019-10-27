<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Users;
use App\Form\ForgotPasswordType;
use App\Form\RegistrationType;
use App\Utils\Exp;
use App\Utils\ExpLibs\Referal;
use Doctrine\ORM\EntityManagerInterface;
use Swift_Mailer;
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
     *
     * @param Request $request
     * @param Security $security
     * @param EntityManagerInterface $em
     * @param UserPasswordEncoderInterface $encoder
     *
     * @param Referal $referal
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function registration(Request $request, Security $security, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder, Referal $referal)
    {
        if ($security->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('index');
        }

        $user = new Users();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $invided = $_COOKIE['ref_id'] ?? 0;
            $user
                ->setPassword($encoder->encodePassword($user, $form->get('password')->getData()))
                ->setInvited($invided)
                ->setRefHash(md5($form->get('email')->getData() . $form->get('login')->getData()));
            $em->persist($user);

            if (!empty($invided) && empty($user->getId())) {
                /** @var Users $invidedUser */
                $invidedUser = $em->find(Users::class, $invided);
                $referal->add($invidedUser, $user);
            }
            $em->flush();
            return $this->redirectToRoute('security_login');
        }
        return $this->render('security/registration.html.twig', [
            'controller_name' => 'IndexController',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/forgotpass", name="security_forgotpass")
     *
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param Swift_Mailer $mailer
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
     */
    public function forgotpass(Request $request, EntityManagerInterface $em, Swift_Mailer $mailer)
    {
        $form = $this->createForm(ForgotPasswordType::class);
        $form->handleRequest($request);
//        if ($form->isSubmitted() && $form->isValid()) {
//            $email = $form->getData();
//            $em->getRepository(Users::class)
//                ->findOneBy(array('email' => $email['email']));
//
//            $identifier = random_bytes(10);
//
//            $url = $this->generateUrl('reset_password', array('email' => $email['email'], 'identifier' => $identifier));
//
//            return $this->redirectToRoute('url');
//        }

        $message = (new \Swift_Message('Hello Email'))
            ->setFrom('send@example.com')
            ->setTo('bootta@yandex.ru')
            ->setBody('test',
//                $this->renderView(
//                // templates/emails/registration.html.twig
//                    'emails/registration.html.twig',
//                    ['name' => 'vasya']
//                ),
                'text/html'
            );

        $mailer->send($message);


        return $this->render('security/registration.html.twig', [
            'controller_name' => 'IndexController',
            'form' => $form->createView(),
        ]);
    }

    /**
     * Переход по реферальной ссылки
     * 
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
