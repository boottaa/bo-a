<?php

namespace App\Controller\Admin;

use App\Entity\News;
use App\Entity\Users;
use App\Entity\UsersLogs;
use App\Form\AddEditNewsType;
use App\Repository\NewsRepository;
use App\Security\NewsVoter;
use App\Utils\Exp;
use App\Utils\ExpLibs\AddedNews;
use App\Utils\Slugger;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class NewsController
 *
 * @Route("/admin/news")
 * @IsGranted("ROLE_REDACTOR")
 * @package App\Controller\Admin
 */
class NewsController extends AbstractController
{
    /**
     * @Route("/", name="adm_news")
     * @Cache(smaxage="1")
     *
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param NewsRepository $news
     * @return Response
     * @throws Exception
     */
    public function index(Request $request, EntityManagerInterface $em, NewsRepository $news): Response
    {
        $page = $request->get('page', 1);
        $pagination = $news->findLatestForAdmin($page, $this->getUser());

        /**
         * @var News $item ;
         */
        foreach ($pagination->getResults() as &$item) {
            if ($this->getUser() === $item->getAuthor()) {
                $item->addAction('Редактировать', $this->generateUrl('adm_news_edit', ['id' => $item->getId()]));
                $item->addAction(
                    'Удалить',
                    $this->generateUrl('news_view', ['id' => $item->getId(), 'text' => $item->getSlug()])
                );
            }
            if ($this->isGranted('ROLE_MODERATOR')) {
                switch ($item->getStatus()) {
                    case News::STATUS_IS_NEW:
                        $item->addAction(
                            'Опубликовать',
                            $this->generateUrl('adm_news_is_publish', ['id' => $item->getId(),
                                'status' => News::STATUS_IS_PUBLISH])
                        );
                        $item->addAction(
                            'Отклонить',
                            $this->generateUrl(
                                'adm_news_is_publish',
                                ['id' => $item->getId(), 'status' => News::STATUS_IS_CANCEL]
                            )
                        );
                        break;
                    case News::STATUS_IS_PUBLISH:
                        $item->addAction(
                            'Снять с публикации',
                            $this->generateUrl(
                                'adm_news_is_publish',
                                ['id' => $item->getId(), 'status' => News::STATUS_IS_CANCEL]
                            )
                        );
                        break;
                    case News::STATUS_IS_CANCEL:
                    default:
                        $item->addAction(
                            'Опубликовать',
                            $this->generateUrl(
                                'adm_news_is_publish',
                                ['id' => $item->getId(), 'status' => News::STATUS_IS_PUBLISH]
                            )
                        );
                        break;
                }
            }
            switch ($item->getStatus()) {
                case News::STATUS_IS_NEW:
                    $item->addBadge('Новое', 'badge-primary');
                    break;
                case News::STATUS_IS_PUBLISH:
                    $item->addBadge('Опубликовано', 'badge-success');
                    break;
                case News::STATUS_IS_CANCEL:
                default:
                    $item->addBadge('Снято с публикации', 'badge-danger');
                    break;
            }
            if ($item->getPublishedAt() > (new \DateTime())) {
                $item->addBadge('Отложенная ' . $item->getPublishedAt()->format('d.m.Y H:i'), 'badge-info');
            }
        }

        return $this->render('admin/news/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/add", name="adm_news_add")
     * @Route("/edit/{id<\d+>}", name="adm_news_edit")
     * @Cache(smaxage="1")
     *
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param Slugger $slugger
     * @param News|null $news
     * @return Response
     * @throws Exception
     */
    public function add(Request $request, EntityManagerInterface $em, Slugger $slugger, News $news = null): Response
    {
        /**
         * @var Users $user
         */
        $user = $this->getUser();
        if (empty($news)) {
            $news = new News();
            $news->setAuthor($user);
        } else {
            $this->denyAccessUnlessGranted(NewsVoter::EDIT, $news, "Вы можете редактировать только свои новости");
        }

        $form = $this->createForm(AddEditNewsType::class, $news);
        //Дату публикации нельзя редактировать
        if (!empty($news->getId())) {
            $form->remove('publishedAt');
        }
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $news->setStatus(News::STATUS_IS_NEW);

            //START UPLOAD FILE
            /**
             * @var UploadedFile $file
             */
            $file = $form['img']->getData();
            if (!empty($file)) {
                $dir = __DIR__ . '/../../../public/img/';
                $extension = $file->guessExtension();
                if (!$extension) {
                    // расширение не может быть угадано
                    $extension = 'bin';
                }
                $fileUploaded = $file->move($dir, md5(random_int(1, 99999)) . '.' . $extension);
                $news->setImg('/img/' . $fileUploaded->getFilename());
            }
            //END UPLOAD FILE

            if (!empty($news->getId())) {
                $news->setUpdatedAt(new \DateTime());
            }
            $news->setSlug($slugger->slugify($form['title']->getData()));
            $em->persist($news);
            $em->flush();
            return $this->redirectToRoute('adm_news');
        }
        return $this->render('admin/form.html.twig', [
            'controller_name' => 'IndexController',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @IsGranted("ROLE_MODERATOR")
     * @Route("/publish/{id<\d+>}/{status<\d+>}", requirements={"status": "10|20|30"}, name="adm_news_is_publish")
     * @Cache(smaxage="1")
     *
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param AddedNews $addedNews
     * @param News $news
     * @param int $status
     * @return Response
     * @throws Exception
     */
    public function isPublish(Request $request, EntityManagerInterface $em, AddedNews $addedNews, News $news, int $status): Response
    {
        if ($news->getStatus() === News::STATUS_IS_NEW) {
            $author = $news->getAuthor();

            $addedNews->add($author, $news);
        }
        $news
            ->setStatus($status)
            ->setUpdatedAt(new \DateTime());
        $em->flush();
        return $this->redirectToRoute('adm_news');
    }
}
