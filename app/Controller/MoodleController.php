<?php

namespace Controller;

use Core\Request;
use Services\MoodleServices;

class MoodleController
{
    private MoodleServices $moodle;

    public function __construct(MoodleServices $moodle)
    {
        $this->moodle = $moodle;
    }

    public function getGrade(Request $request)
    {
        return $this->moodle->getGrade($request->username, $request->password);
    }
}