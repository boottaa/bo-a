<?php

namespace App\Command;

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
     * RamblerCrawlerCommand constructor.
     */
    public function __construct()
    {
        $this->httpClient = HttpClient::create();
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

//        dd($this->links);
        return $this;
    }

    /**
     * @param $url
     * @return string
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    private function getNews()
    {
        foreach ($this->links as $item) {

            $response = $this->httpClient->request('GET', $item['link']);
            $content = $response->getContent();
            $crawler = new Crawler($content);

            $content = $crawler->filter('div.article__content')->html();
            $h1 = $crawler->filter('h1.big-title__title')->text();
            $description = $crawler->filter('meta[itemprop="description"]')->attr('content');
            $img = $crawler->filter('link[itemprop="image"]')->attr('content');

            dd($img);

            if(empty($img)){
                continue;
            }
            dd($img);
        }
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
