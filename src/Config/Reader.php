<?php

namespace Bolt\Config;

class Reader
{
    protected $filename;
    protected $configData;

    public function __construct($filename = null)
    {
        if (!defined("BASE_PATH"))
            throw new \Exception('Please Define a BASE_PATH constant in your index file on docroot');

        if (!is_null($filename))
            $this->load($filename);
    }

    public function load($filename): Reader
    {
        $this->setFilename($filename);
        $this->setConfigData(include($this->getFilename()));
        foreach ($this->getConfigData() as $key => $value) {
            $this->{$key} = $value;
        }
        return $this;
    }

    /**
     * @param mixed $configData
     */
    private function setConfigData($configData): void
    {
        unset($this->configData);
        $this->configData = $configData;
    }

    public function get($key): string
    {
        if($this->hasKey($key))
            return $this->configData[$key];
        else
            return NULL;
    }

    public function hasKey($key): bool
    {
        if(isset($this->configData[$key]))
            return TRUE;
        else
            return FALSE;
    }

    /**
     * @return mixed
     */
    private function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * @param mixed $filename
     */
    private function setFilename($filename): void
    {
        if (!file_exists($filename))
            $this->filename = BASE_PATH . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . $filename . '.php';

        if (!file_exists($this->filename))
            throw new \Exception("the config file : $filename is not present in path $this->filename");
    }

    /**
     * @return mixed
     */
    private function getConfigData(): array
    {
        return $this->configData;
    }


}