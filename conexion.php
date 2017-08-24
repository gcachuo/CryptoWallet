<?php

/**
 * Created by PhpStorm.
 * User: Cachu
 * Date: 21/feb/2017
 * Time: 06:14 PM
 */

/**
 * Class Conexion
 * @var mysqli $conexion
 */
abstract class Conexion
{
    static $host, $db, $user, $pass;
    static private $conexion;
    private $retry;

    protected function conectar()
    {
        if (is_null(self::$conexion->sqlstate)) {
            self::$conexion = mysqli_connect(self::$host, self::$user, self::$pass, self::$db);
        }
    }

    protected function desconectar()
    {
        mysqli_close(self::$conexion);
    }

    function __destruct()
    {
        self::$host = self::$db = self::$user = self::$pass = "";
    }

    /**
     * @param string $sql
     * @return int|mysqli_result
     */
    protected function consulta($sql)
    {
        $this->retry = true;
        $resultado = null;
        while ($this->retry) {
            try {
                $this->conectar();
                $resultado = mysqli_query(self::$conexion, $sql);
                if (is_bool($resultado) and $resultado != false) {
                    $resultado = mysqli_insert_id(self::$conexion);
                }
                $this->desconectar();
                $this->retry = false;
            } catch (mysqli_sql_exception $ex) {
                $this->handleErrors($ex->getCode(), $ex->getMessage());
            }
        }
        return $resultado;
    }

    function __get($name)
    {
        $name = rtrim($name, "_");
        include_once "modelo/tablas/$name.php";
        $tabla = "Tabla$name";
        $class = new $tabla();
        return $class;
    }

    private function handleErrors($code, $message)
    {
        /** @var bd $this */
        switch ($code) {
            case 1146:
                /** @var bd $table */
                $token = strtolower($_SESSION[token]) ?: Conexion::$db;
                $table = trim(str_replace("Table '$token.", "", str_replace("' doesn't exist", "", $message)), "_");
                if (is_null($this->$table->create_table())) {
                    $this->retry = false;
                    Globales::mensaje_error("No se creo la tabla $table");
                }
                break;
            case 1054:
                $table = str_replace("Tabla", "", get_class($this));
                if (method_exists($this->$table, "modify_table")) {
                    $this->retry = false;
                    if (is_null($this->$table->modify_table())) {
                        Globales::mensaje_error("No se modificó la tabla $table. $message");
                    }
                } else {
                    $this->retry = false;
                    Globales::mensaje_error($message);
                }
                break;
            case 1060:
                $this->retry = false;
                break;
            default:
                $this->retry = false;
                Globales::mensaje_error($message);
                break;
        }
    }

    /**
     * @param mysqli_result $consulta
     * @return null|object
     */
    protected function siguiente_registro($consulta)
    {
        return mysqli_fetch_object($consulta);
    }

    /**
     * @param $sql
     * @return bool|mysqli_result
     */
    protected function multiconsulta($sql)
    {
        $this->conectar();
        mysqli_multi_query(self::$conexion, $sql);
        do {
            null;
        } while (mysqli_more_results(self::$conexion) && mysqli_next_result(self::$conexion));
        $result = mysqli_store_result(self::$conexion);
        return $result;
    }

    /**
     * @param mysqli_result $consulta
     * @return array
     */
    protected function query2multiarray($consulta)
    {
        $results = mysqli_fetch_all($consulta, MYSQLI_ASSOC);
        return $results;
    }
}

abstract class bd extends Conexion
{
    /**
     * bd constructor.
     * @param string $token
     */
    function __construct($token)
    {
        $token = $token ?: ($_SESSION[token] ?: "cbizgastos");
        Conexion::$host = "localhost";
        Conexion::$db = "crypto";
        Conexion::$user = "cachu";
        Conexion::$pass = "0908070605mM*";
    }

    public function __call($name, $arguments)
    {
        if (!method_exists($this, $name)) {
            Globales::mensaje_error("No existe el metodo $name.");
        }
    }

    abstract function create_table();
}