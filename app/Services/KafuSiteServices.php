<?php

namespace Services;

use Core\HttpRequest\HttpRequest;
use Symfony\Component\DomCrawler\Crawler;

class KafuSiteServices
{
    public function getNewsAction($page = 1)
    {
        $crawler = new Crawler(file_get_contents(env('KAFU_NEWS') . $page));
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