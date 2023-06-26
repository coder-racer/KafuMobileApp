<?php

namespace Controller;

use Services\Moodle;

class MoodleController
{
    private Moodle $moodle;

    public function __construct()
    {
        $this->moodle = new Moodle();
    }

    public function getGrade()
    {
        return $this->moodle->getGrade($_POST['username'], $_POST['password']);
    }
}