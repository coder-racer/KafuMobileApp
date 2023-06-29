<?php

namespace Controller;

use Core\Request;
use Services\DocumentServices;
use TCPDF;

class DocumentController
{
    public function __construct(private readonly DocumentServices $documentServices)
    {
    }

    public function getDocument(Request $request): void
    {
        $this->documentServices->getDocument($request->name);
    }

    public function getListDocs(): array
    {
       return $this->documentServices->getListDocs();
    }


}