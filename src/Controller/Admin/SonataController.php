<?php
namespace App\Controller\Admin;

use App\Entity\Users;
use App\Form\RegistrationType;
use App\Form\Type\TagsInputType;
use App\Repository\UsersLogsRepository;
use App\Repository\UsersRepository;
use App\Utils\Exp;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
     *
     * @param Request $request
     * @param UsersRepository $users
     * @param UsersLogsRepository $usersLogsRepository
     * @param Exp $exp
     * @return Response
     */
    public function index(Request $request, UsersRepository $users, UsersLogsRepository $usersLogsRepository, Exp $exp): Response
    {
        /**
         * @var Users $user;
         */
        $user = $this->getUser();

        $ref_link = $this->generateUrl('ref', ['ref_hash' => $user->getRefHash()], UrlGeneratorInterface::ABSOLUTE_URL);
        $referals = $users->findBy(['invited' => $user->getId()]);
        [$level, $next_exp] = $exp->getLevel($user);

        return $this->render(
            'admin/index.html.twig',
            [
                'ref_link' => $ref_link,
                'ref_count' => \count($referals),
                'followers' => \count($user->getFollowers()),
                'subscribes' => \count($user->getSubscribedTo()),
                'level' => $level,
                'next' => $next_exp,
                'exp' => $user->getExp(),
                'logs' => $usersLogsRepository->findLatest($user->getId())->paginate()->getResults(),
                'progress' => ($user->getExp() - 100) / $next_exp * 100
            ]
        );
    }

    /**
     * @Route("/edit_profile", methods={"GET", "POST"}, name="adm_edit_profile")
     * @Cache(smaxage="1")
     *
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return RedirectResponse|Response
     */
    public function editProfile(Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('index');
        }

        /**
         * @var Users $theuser
         */
        $theuser = $this->getUser();
        $theform = $this
            ->createForm(RegistrationType::class, $theuser)
            ->add('hobbies', TagsInputType::class, [
                'label' => 'Увлечения:',
                'help' => 'Укажите не более 6 ваших увлечений и интересов',
                'attr' => ['class' => 'tokenfield'],
            ])->remove('password');

        $theform->handleRequest($request);

        if ($theform->isSubmitted() && $theform->isValid()) {
            $em->persist($theuser);
            $em->flush();
            return $this->redirectToRoute('adm_index');
        }
        return $this->render('admin/form.html.twig', [
            'controller_name' => 'IndexController',
            'form' => $theform->createView()
        ]);
    }

}