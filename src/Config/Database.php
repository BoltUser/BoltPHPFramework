<?php

namespace Bolt\Config;

class Cache extends Reader
{

    protected $username;
    protected $password;
    protected $host;
    protected $port;
    protected $adapter;

    public function __construct()
    {
        $this->setFilename('cache');
        $this->load();
    }


}