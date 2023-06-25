<?php

namespace Controller;

use Services\Platonus;

class PlatonusController
{
    private Platonus $platonus;

    public function __construct()
    {
        $this->platonus = new Platonus();
    }

    public function getUserData(): array
    {
        return $this->platonus->getUserDataAction();
    }

    public function getJournal(): array
    {
        return $this->platonus->getJournalAction();
    }

    public function getNews(): array
    {
        return $this->platonus->getNewsAction();
    }

    public function login(): array
    {
        return $this->platonus->loginAction();
    }
}