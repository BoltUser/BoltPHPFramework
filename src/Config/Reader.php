<?php

namespace Bolt\Config;

class Reader
{
    public const BASE_PATH = '';
    protected $filename;
    protected $configData;

    public function __construct()
    {

    }

    public function load()
    {
        if(!isset($this->filename))
            throw new \Exception('Please specify a config Filename');

        $configData = include_once($this->filename);
        $this->setConfigData($configData);
    }

    /**
     * @param mixed $configData
     */
    private function setConfigData($configData): void
    {
        $this->configData = $configData;
    }

    public function get($key)
    {
        if($this->hasKey($key))
            return $this->configData[$key];else
            return NULL;
    }

    public function hasKey($key)
    {
        if(isset($this->configData[$key]))
            return TRUE;else
            return FALSE;
    }

    /**
     * @return mixed
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @param mixed $filename
     */
    public function setFilename($filename): void
    {
        if(!defined("BASE_PATH"))
            throw new \Exception('Please Define a BASE_PATH constant in your index file on docroot');

        $this->filename = BASE_PATH . DIRECTORY_SEPARATOR . 'Config' . DIRECTORY_SEPARATOR . $filename . '.php';
        var_dump($this->filename);
    }

    /**
     * @return mixed
     */
    public function getConfigData()
    {
        return $this->configData;
    }


}