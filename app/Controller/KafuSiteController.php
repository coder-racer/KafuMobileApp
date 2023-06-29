<?php

namespace Controller;

use Core\Request;
use Services\KafuSiteServices;

class KafuSiteController
{

    public function __construct(private readonly KafuSiteServices $kafuSiteServices)
    {
    }

    public function getNews(Request $request): array
    {
        return $this->kafuSiteServices->getNewsAction();
    }
}