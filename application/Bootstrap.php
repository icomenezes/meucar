<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

    protected function _initTimezone()
    {
        date_default_timezone_set('America/Sao_Paulo');
    }

    protected function _initConfig()
    {
        $config = new Zend_Config($this->getOptions());
        Zend_Registry::set('config', $config);
        return $config;
    }

    protected function _initDbSqlMode()
    {
        $this->bootstrap('db');
        $db = $this->getResource('db');
        $db->query("SET NAMES utf8mb4");
        $db->query("SET SESSION sql_mode=''");
    }

}

