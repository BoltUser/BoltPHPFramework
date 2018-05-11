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
    public $segmentName;

    public function __construct($cookie = FALSE, $segmentName = 'session')
    {
        session_start();

        $sessionFactory = new SessionFactory();
        $this->segmentName = $segmentName;
        if($cookie)
            $this->session = $sessionFactory->newInstance($_COOKIE);
        else
            $this->session = $sessionFactory->newInstance($_SESSION);

        $this->setSegment($this->segmentName);
    }

    public function setSegment($segmentName)
    {
        $this->segment = $this->session->getSegment($segmentName);
    }

    public function set($key, $value)
    {
        $this->segment->set($key,$value);
    }

    public function get($key)
    {
        return $this->segment->get($key);
    }

    public function setFlash($key, $value)
    {
        $this->segment->setFlash($key, $value);
    }

    public function getFlash($key)
    {
        return $this->segment->getFlash($key); // 'Hello world!'
    }

    public function regenerateId()
    {
        $this->session->regenerateId();
    }

    public function setLifetime($lifetime = '1209600')
    {
        $this->session->setCookieParams(array('lifetime' => $lifetime));
    }

    public function clear()
    {
        $this->session->clear();
    }

    public function commit()
    {
        $this->session->commit();
    }

    public function destroy()
    {
        $this->session->destroy();
    }

}