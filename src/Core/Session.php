<?php
/**
 * Date: 27/12/17
 * Time: 21.01
 */

namespace Bolt\Core;


use Aura\Session\SessionFactory;

class Session
{
    private $session;
    private $segment;

    public function __construct($cookie=FALSE)
    {
        $sessionFactory = new SessionFactory();

        if($cookie)
            $this->session = $sessionFactory->newInstance($_COOKIE);
        else
            $this->session = $sessionFactory->newInstance($_SESSION);

        $this->setSegment('sitename');
    }

    public function setSegment($segmentName):void
    {
        $this->segment = $this->session->getSegment($segmentName);
    }

    public function set($key,$value):void
    {
        $this->segment->set($key,$value);
    }

    public function get($key):mixed
    {
        $this->segment->get($key);
    }

    public function setFlash($key,$value):void
    {
        $this->segment->setFlash($key, $value);
    }

    public function getFlash($key):mixed
    {
        return $this->segment->getFlash($key); // 'Hello world!'
    }

    public function regenerateId():void
    {
        $this->session->regenerateId();
    }

    public function setLifetime($lifetime='1209600'):void
    {
        $this->session->setCookieParams(array('lifetime' => $lifetime));
    }

    public function clear():void
    {
        $this->session->clear();
    }

    public function commit():void
    {
        $this->session->commit();
    }

    public function destroy():void
    {
        $this->session->destroy();
    }

}