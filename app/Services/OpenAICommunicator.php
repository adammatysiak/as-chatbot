<?php

namespace App\Services;

interface OpenAICommunicator
{

    public function ask($content, $params = []);

}