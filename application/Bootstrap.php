<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

    protected function _initDbSqlMode()
    {
        $this->bootstrap('db');
        $db = $this->getResource('db');
        $db->query("SET SESSION sql_mode=''");
    }

}

