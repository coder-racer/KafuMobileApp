<?php

namespace Services;

use Core\HttpRequest\HttpRequest;
use Symfony\Component\DomCrawler\Crawler;

class KafuSiteServices
{
    public function __construct(private HttpRequest $httpRequest)
    {
    }

    public function getNewsAction($page = 1): array
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

    public function getNew($link): array
    {
        $this->httpRequest->setUrl($link);
        $response = $this->httpRequest->get();
        $crawler = new Crawler($response->getContent());
        $containerContent = $crawler->filter('.content-body .article-content');

        $images = [];

        $containerContent->filter('img')->each(function (Crawler $node) use (&$images) {
            $dataSrc = $node->attr('data-src');
            $images[] = $dataSrc;
            if (!empty($dataSrc)) {
                $node->getNode(0)->setAttribute('src', $dataSrc);
            }
        });
        // Удаляем теги <script> и <noscript> из содержимого
        $containerContent->filter('script')->each(function (Crawler $node) {
            $node->getNode(0)->parentNode->removeChild($node->getNode(0));
        });

        $containerContent->filter('noscript')->each(function (Crawler $node) {
            $node->getNode(0)->parentNode->removeChild($node->getNode(0));
        });

        // Удаляем блоки с классом "uSocial-Share" из содержимого
        $containerContent->filter('.uSocial-Share')->each(function (Crawler $node) {
            $node->getNode(0)->parentNode->removeChild($node->getNode(0));
        });

        $cleanedContent = $containerContent->html();

        return ['data' => $cleanedContent, 'images' => $images];
    }
}