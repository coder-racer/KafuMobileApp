<?php

namespace Controller;

use Core\Request;
use Services\PlatonusServices;

class PlatonusController
{
    private PlatonusServices $platonus;

    public function __construct(PlatonusServices $platonus)
    {
        $this->platonus = $platonus;
    }

    public function getUserData(Request $request): array
    {
        return $this->platonus->getUserDataAction($request->token);
    }

    public function getJournal(Request $request): array
    {
        return $this->platonus->getJournalAction(
            $request->year,
            $request->academic,
            $request->JSESSIONID,
            $request->token,
            $request->userId
        );
    }

    public function login(Request $request): array
    {
        return $this->platonus->loginAction($request->login, $request->pass);
    }
}