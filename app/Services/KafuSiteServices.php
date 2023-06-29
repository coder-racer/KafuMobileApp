<?php

namespace Services;

use core\HttpRequest\HttpRequest;
use Symfony\Component\DomCrawler\Crawler;

class KafuSiteServices
{
    public function __construct(private HttpRequest $httpRequest)
    {

    }

    public function getNewsAction($page = 1)
    {
        $this->httpRequest->setUrl(env('KAFU_NEWS') . $page);
        $response = $this->httpRequest->get();

        $crawler = new Crawler($response->getContent());
        return $crawler->filter('.article-content.posts-list a')->each(function ($node) {
            return [
                'title' => $node->filter('h2')->text(),
                'url' => $node->attr('href'),
                'img' => $node->filter('img')->attr('data-src'),
                'text' => $node->filter('.desc')->text(),
            ];
        });
    }

}