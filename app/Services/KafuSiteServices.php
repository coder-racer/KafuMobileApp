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
            $titleNode = $node->filter('h2');
            $imgNode = $node->filter('img');
            $descNode = $node->filter('.desc');

            return [
                'title' => $titleNode->count() > 0 ? $titleNode->text() : '',
                'url' => $node->attr('href') ?? '',
                'img' => $imgNode->count() > 0 ? $imgNode->attr('data-src') : null,
                'text' => $descNode->count() > 0 ? $descNode->text() : '',
            ];
        });

    }

}