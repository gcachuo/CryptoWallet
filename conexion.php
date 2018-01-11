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
     * @throws Exception
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
                $this->handleErrors($ex, $sql);
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

    /**
     * @param Exception $ex
     * @param $sql
     * @throws Exception
     */
    private function handleErrors($ex, $sql)
    {
        $code = $ex->getCode();
        $message = $ex->getMessage();
        $trace = $ex->getTrace();
        /** @var Tabla $this */
        switch ($code) {
            case 1146:
                /** @var Tabla $table */
                $token = "crypto";
                $table = trim(str_replace("Table '$token.", "", str_replace("' doesn't exist", "", $message)), "_");
                $verificar = is_null($this->$table->create_table());
                if ($verificar) {
                    $this->retry = false;
                    Globales::mensaje_error("No se creo la tabla $table");
                }
                break;
            case 1054:
                $table = str_replace("distribuidor\\", "", str_replace("Tabla", "", get_class($this)));
                if (method_exists($this->$table, "modify_table")) {
                    $this->retry = false;
                    if (is_null($this->$table->modify_table())) {
                        Globales::mensaje_error("No se modificó la tabla $table. $message");
                    }
                } else {
                    $this->retry = false;
                    Globales::mensaje_error($message . " [" . get_class($this) . "]");
                }
                break;
            case 1060:
                $this->retry = false;
                break;
            case 1064:
                /** Error de sintaxis */
                $this->retry = false;
                Globales::mensaje_error("Error 1064. Contacte al desarrollador. [{$trace[2]['class']}]");
                break;
            case 2002:
                /** Error de conexion */
                $this->retry = false;
                Globales::mensaje_error("Error 2002. Verifique su conexion.");
                break;
            case 2006:
                $this->retry = true;
                mysqli_ping(self::$conexion);
                Globales::mensaje_error('Error 2006. Intente de nuevo. [MySQL Server Has Gone Away]');
                break;
            default:
                $this->retry = false;
                Globales::mensaje_error("Error $code. $message");
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

abstract class Tabla extends Conexion
{
    /**
     * cbiz constructor.
     * @param string $token
     */
    function __construct($token)
    {
        $config = Globales::getConfig()->conexion;
        $token = $token ?: $config->default_database;
        Conexion::$host = $config->host;
        Conexion::$db = "{$config->prefix}$token";
        Conexion::$user = $config->user;
        Conexion::$pass = $config->password;
    }

    public function __call($name, $arguments)
    {
        if (!method_exists($this, $name)) {

        }
    }

    /**
     * @return bool regresar la consulta
     */
    abstract function create_table();
}