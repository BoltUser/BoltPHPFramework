<?php

namespace Bolt\Controller;

use Aura\Web\Request;
use Aura\Web\Response;

class BoltController
{

    public function __construct(Request $request, Response $response, Session $session)
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