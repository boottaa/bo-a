<?php

namespace App\Command;

use App\Entity\Img;
use App\Entity\News;
use App\Entity\Tags;
use App\Entity\Users;
use App\Repository\TagsRepository;
use App\Repository\UsersRepository;
use App\Utils\Slugger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;

class RamblerCrawlerCommand extends Command
{
    protected static $defaultName = 'RamblerCrawler';

    /**
     * @var \Symfony\Contracts\HttpClient\HttpClientInterface
     */
    private $httpClient;

    private $links = [];

    private const LINK = 'https://news.rambler.ru';
    private const LIST_LINK = self::LINK . '/latest/';
    private const TEGS = [
        'world' => 'В мире',
        'moscow_city' => 'Новости москвы',
        'politics' => 'Политика',
        'community' => 'Общество',
        'incidents' => 'Проишествия',
        'tech' => 'Наука и техника',
        'starlife' => 'Шоу-бизнес',
        'army' => 'Армия',
        'articles' => 'Статьи'
    ];

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var UsersRepository
     */
    private $usersRepository;

    /**
     * @var Slugger
     */
    private $slugger;

    /**
     * @var TagsRepository
     */
    private $tagsRepository;

    /**
     * RamblerCrawlerCommand constructor.
     */
    public function __construct(EntityManagerInterface $entityManager, UsersRepository $usersRepository, TagsRepository $tagsRepository, Slugger $slugger)
    {
        $this->httpClient = HttpClient::create();
        $this->entityManager = $entityManager;
        $this->usersRepository = $usersRepository;
        $this->slugger = $slugger;
        $this->tagsRepository = $tagsRepository;
        parent::__construct();
    }


    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    private function getLinks()
    {
        $response = $this->httpClient->request('GET', self::LIST_LINK);
        $content = $response->getContent();
        $crawler = new Crawler($content);
        $r = $crawler->filter('a.article-summary__ghost')->getIterator();

        foreach ($r as $i){
            $link = $i->attributes['href']->value;
            $matches = [];
            preg_match('|\/(\w+)\/\d+-(.*)\/|i', $link, $matches);
            if (empty(self::TEGS[$matches[1]])) {
                continue;
            }

            $this->links[] = [
                'link' => self::LINK . $link,
                'tag' =>  self::TEGS[$matches[1]],
                'slug' => $matches[2],
            ];
        }
        return $this;
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    private function getNews()
    {
        /** @var Users $user */
        $user = $this->getUser();

        foreach ($this->links as $item) {

            $response = $this->httpClient->request('GET', $item['link']);
            $content = $response->getContent();
            $crawler = new Crawler($content);
            $tags = [];

            $text = $crawler->filter('div.article__content')->html();
            $h1 = str_replace(['&nbsp'], [' '], trim($crawler->filter('h1.big-title__title')->text()));
            $description = $crawler->filter('meta[itemprop="description"]')->attr('content');
            $img = $crawler->filter('link[itemprop="image"]')->attr('content');
            /** @var Tags $tags */
            $tags[] = $this->getTag($item['tag']);
            preg_match_all('|<a (.*)>(.*)(<\/a>)|iU', $text, $matches);

            //Убираем лишние теги банеров
            $text = preg_replace('|<div class="article__banner[a-zA-Z0-9_\-\"\>\<\=\/\s]+<\/div>\s<\/div>|miU', '', $text);

            $fullLinks = $matches[0];
            $textLinks = $matches[2];

            foreach ($textLinks as $tagItem){
                if (preg_match('|^[^<div][\w\s\-]++|', trim($tagItem))) {
                    $tags[] = $this->getTag($tagItem);
                }
            }
            $afterReplaceText = str_replace($fullLinks, $textLinks, $text);

            $img = (new Img())->setUrl($img)->setName($img)->setType('');
            $this->entityManager->persist($img);
            $news = (new News())
                ->setTitle($h1)
                ->setDescription($description)
                ->setStatus(News::STATUS_IS_PUBLISH)
                ->setImg($img)
                ->setText($afterReplaceText)
                ->setAuthor($user)
                ->setSlug($this->slugger->slugify($h1))
                ->addTag(...$tags)
            ;
            $this->entityManager->persist($news);
            $this->entityManager->flush();
        }
    }

    /**
     * @return Users
     */
    private function getUser(): Users
    {
        $user = $this->usersRepository->findOneBy(['login' => 'botRambler']);

        if (empty($user)) {
            $user = (new Users())
                ->setEmail('bot-rambler@co.com')
                ->setFName('Алексей')
                ->setLName('Шуршов')
                ->setLogin('botRambler')
                ->setPassword('t')
                ->setRefHash('')
                ->setInvited(0)
                ->setRole(12)
                ->setStatus(1)
            ;
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }

        return $user;
    }

    private function getTag(string $item): Tags
    {
        $tag = $this->tagsRepository->findOneBy(['name' => $item]);

        if (empty($tag)) {
            $tag = (new Tags())
                ->setName($item);
            $this->entityManager->persist($tag);
            $this->entityManager->flush();
        }

        return $tag;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);



        $this->getLinks()->getNews();

        dd($this->links);
        die();

        //$this->init('https://news.rambler.ru/world/42939565-delo-mh-17-niderlandy-mstyat-ukraine/');

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

    }


}
