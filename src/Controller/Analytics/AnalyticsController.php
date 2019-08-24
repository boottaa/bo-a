<?php

namespace App\Controller\Analytics;

use App\Entity\News;
use App\Utils\SaveEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/analytics")
 */
class AnalyticsController extends AbstractController
{
    const FILE_STAT_NEWS = __DIR__ . '/../../../stat/news.php';

    /**
     * @Route("/", name="analytics")
     */
    public function index()
    {
        return $this->render('analytics/index.html.twig', [
            'controller_name' => 'AnalyticsController',
        ]);
    }

    /**
     * Check unique visitor
     *
     * @Route("/event/news/{id<\d+>}", name="analytics_counter")
     */
    public function counter(EntityManagerInterface $em, SaveEvent $event, News $news)
    {
        $event->setFile(self::FILE_STAT_NEWS);

        $user = $this->getUser() ? $this->getUser()->getLogin() : ($_SERVER['SERVER_ADDR'] . $_SERVER['HTTP_USER_AGENT']);
        $hash = md5($news->getId() . $user);
        $data = ['success' => false];

        if ($event->has($hash)) {
            $data['success'] = false;
        } else {
            $event->add($hash, 1);
            $news->setUniqueVisitors($news->getUniqueVisitors() + 1);
            $data['success'] = true;
        }

        $news->setVisitors($news->getVisitors() + 1);
        $em->persist($news);
        $em->flush();

        return new JsonResponse($data);
    }
}
