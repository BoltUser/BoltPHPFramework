<?php

namespace Bolt\Config;


class Cache extends Reader
{

    public function __construct()
    {
        $this->setFilename('cache');
        $this->load();
    }

}