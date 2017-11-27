<?php

namespace Bolt\Config;


class Manager
{

    public function __construct()
    {
    }

    public function load($configName)
    {
        if(!isset($configName))
            throw new \Exception('Config name not specified');

        var_dump(DIR);

    }


}