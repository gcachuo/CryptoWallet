<?php
/**
 * Created by PhpStorm.
 * User: Cachu
 * Date: 13/abr/2017
 * Time: 01:43 PM
 */

namespace distribuidor;


class DbCbizControl extends \cbizcontrol
{
    function createNewDatabase($token)
    {
        $sql = /** @lang MySQL */
            <<<MySQL
CREATE DATABASE e11_$token;
MySQL;
        $this->consulta($sql);
    }

    function createTables($token)
    {
        $sql = /** @lang MySQL */
            <<<MySQL
USE e11_$token;

MySQL;
        $files = glob("modelo/sql/alta/*.sql");
        $files = array_reverse($files);
        $sql .= file_get_contents($files[0]);

        $this->multiconsulta($sql);
    }

    function insertEntries($token)
    {
        $sql = /** @lang MySQL */
            <<<MySQL
USE e11_$token;

MySQL;
        $files = glob("modelo/sql/inserts/*.sql");

        foreach ($files as $file) {
            $sql .= file_get_contents($file);
        }

        $this->multiconsulta($sql);
    }
}