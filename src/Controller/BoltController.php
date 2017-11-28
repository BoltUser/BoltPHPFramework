<?php

namespace Bolt\Controller;


class BoltController
{

    public function __construct($request,$response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function beforeRun()
    {

    }

    public function afterRun()
    {

    }

}