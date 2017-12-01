<?php

namespace Bolt\Database;


class Mysql
{

    public function __construct()
    {
        $serviceContainer = \Propel\Runtime\Propel::getServiceContainer();
        $serviceContainer->checkVersion('2.0.0-dev');
        $serviceContainer->setAdapterClass('bookstore','mysql');
        $manager = new \Propel\Runtime\Connection\ConnectionManagerSingle();
        $manager->setConfiguration(array('dsn' => 'mysql:host=localhost;port=3306;dbname=bookstore','user' => 'root','password' => 'mysqlm0rr1an0l1c3001!','settings' => array('charset' => 'utf8','queries' => array(),),'classname' => '\\Propel\\Runtime\\Connection\\ConnectionWrapper','model_paths' => array(0 => 'src',1 => 'vendor',),));
        $manager->setName('bookstore');
        $serviceContainer->setConnectionManager('bookstore',$manager);
        $serviceContainer->setDefaultDatasource('bookstore');
    }

}