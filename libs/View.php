<?php

/**
 * Created by PhpStorm.
 * User: Cachu
 * Date: 05/abr/2017
 * Time: 12:17 PM
 */
class View
{
    private $pagecontent;

    /**
     * @param $name
     * @param array $vars
     * @return $this
     */
    public function show($name, $vars = array())
    {
        $path = "views/$name.phtml";
        if (file_exists($path)) {
            $controller = "{$name}Controller";
            if (file_exists("controller/$controller.php")) {
                include_once "controller/$controller.php";
                $controller = new $controller();
            }

            ob_start();
            include_once $path;
            $this->pagecontent .= ob_get_contents();
            ob_end_clean();
        }
        return $this;
    }

    function __destruct()
    {
        $pagecontent = $this->pagecontent;
        require "views/master.phtml";
        unset($this->pagecontent);
    }
}