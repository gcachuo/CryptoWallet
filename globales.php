<?php

/**
 * Created by PhpStorm.
 * User: Cachu
 * Date: 20/feb/2017
 * Time: 05:03 PM
 */

use Mpdf\Mpdf;

/**
 * @property array monthNames
 * @property string formatoFecha
 * @property string btnNuevo
 * @property string btnEditar
 * @property string btnEliminar
 * @property string btnRegistrar
 * @property string btnGuardar
 * @property string btnProyectar
 * @property string btnSuscripcion
 * @property string lblEgresos
 * @property string lblIngresos
 * @property string lblTraspasos
 * @property string lblTransacciones
 * @property string lblTitulo
 * @property string lblSubtitulo
 * @property string lblIngresosTotales
 * @property string lblEgresosTotales
 * @property string lblEmail
 * @property string lblDistribuidor
 * @property string lblNombre
 * @property string lblApellidoP
 * @property string lblApellidoM
 * @property string lblTelefono
 * @property string lblPassword
 * @property string lblRePassword
 * @property string lblTotal
 * @property string lblReestablecer
 * @property string lblSinCategoria
 * @property string lblReseller
 * @property string lblCuentaOrigen
 * @property string lblCuentaDestino
 */
class Globales
{
    static $version = "1.4.0", $modulo, $namespace;
    static private $idioma, $permisos, $token;

    static function getToken()
    {
        return self::$token;
    }

    static function setToken($token)
    {
        self::$token = $token;
    }

    /**
     * @param string $modulo
     * @return array
     */
    static function getIdioma($modulo)
    {
        return self::$idioma->$modulo;
    }

    /**
     * @param array $idioma
     */
    static function setIdioma($idioma)
    {
        self::$idioma = $idioma;
    }

    /**
     * @param object $permisos
     */
    static function setPermisos($permisos)
    {
        self::$permisos = $permisos;
    }

    /**
     * @param string $modulo
     * @return object
     */
    static function getPermisos($modulo)
    {
        if ($_SESSION[perfil] == 0)
            $permisos = array("nuevo" => 1, "editar" => 1, "eliminar" => 1);
        else {
            $permisos = self::search_in_multi(self::$permisos, "modulo", $modulo);
            $permisos = array_column($permisos, "estatus", "accion");
        }
        return (object)$permisos;
    }

    /**
     * @param string $namespace
     */
    static function setNamespace($namespace)
    {
        self::$namespace = $namespace . "\\";
    }

    /**
     * @param string $mensaje
     * @param int $code
     * @throws Exception
     */
    static function mensaje_error($mensaje, $code = 200)
    {
        throw new Exception($mensaje, $code);
    }

    /** @var Exception $ex */
    static function mostrar_exception($ex)
    {
        $token = $_SESSION[token];
        ini_set('error_log', "script_errors_$token.log");
        $trace = $ex->getTrace();
        $error = addslashes($token . " " . $_SESSION[modulo] . " " . $trace[2][file] . " " . $trace[2][line] . " " . $ex->getMessage());
        $error2 = addslashes(preg_replace("/\r|\n/", "", print_r($ex, true)));
        error_log(print_r($ex, true));
        if (isset($_POST[fn]) or $_GET[aside]) {
            echo $ex->getMessage();
        } else {
            session_unset();
            $_SESSION[modulo] = "login";
            ?>
            Error. Recargue la pagina.
            <script>console.error("<?= $error ?>")</script>
            <?php
        }
        http_response_code($ex->getCode());
    }

    /**
     * @param string $password
     * @return string
     */
    static function crypt_blowfish_bydinvaders($password)
    {
        $salt = '$2a$%02d$' . $password;
        return crypt($password, $salt);
    }

    static function encrypt($pass, $key)
    {
        $iv = openssl_random_pseudo_bytes(16);

        $crypttext = openssl_encrypt($pass, 'RC4', $key);
        return $crypttext;
    }

    static function decrypt($pass, $key)
    {
        $iv = openssl_random_pseudo_bytes(16);

        $crypttext = openssl_decrypt($pass, 'RC4', $key);
        return $crypttext;
    }

    /**
     * @param string $datetime 'Y-m-d'
     * @param int $time_add
     * @param string $interval
     * @param string $format
     * @return string
     */
    static function datetime_add($datetime, $time_add, $interval, $format)
    {

        switch ($interval) {
            case 'years':
                $spec = "P{$time_add}Y";
                break;
            case 'months':
                $spec = "P{$time_add}M";
                break;
            case 'days':
                $spec = "P{$time_add}D";
                break;
            case 'weeks':
                $spec = "P{$time_add}W";
                break;
            case 'hours':
                $spec = "PT{$time_add}H";
                break;
            case 'minutes':
                $spec = "PT{$time_add}M";
                break;
            case 'seconds':
                $spec = "PT{$time_add}S";
                break;
            default:
                $spec = "PT";
                break;
        }

        $time = new DateTime($datetime);
        $time->add(new DateInterval($spec));
        return $time->format($format);
    }

    /**
     * @param string $path
     * @return object
     */
    static function get_json_to_object($path)
    {
        if (!file_exists($path))
            self::mensaje_error("No existe el archivo $path");
        $json = file_get_contents($path);
        return json_decode($json, false);
    }

    /**
     * @param string $path
     * @return object
     */
    static function get_json_to_array($path)
    {
        if (!file_exists($path))
            self::mensaje_error("No existe el archivo $path");
        $json = file_get_contents($path);
        return json_decode($json, true);
    }

    /**
     * @deprecated
     * @param mysqli_result $consulta
     * @return object
     */
    static function query2object($consulta)
    {
        $array = array();
        foreach ($consulta as $item) {
            foreach ($item as $key => $value) {
                $val = next($item);
                if ($val === false) continue;
                $array[$value] = $val;
            }
        }
        return (object)$array;
    }

    /**
     * @deprecated
     * @param mysqli_result $consulta
     * @return object
     */
    static function query2twoLevelObject($consulta)
    {
        $object = (object)array();
        foreach ($consulta as $item) {
            foreach ($item as $key => $arrayKey) {
                $valueKey = next($item);
                $value = next($item);
                if ($value === false) continue;
                $object->$arrayKey->$valueKey = $value;
            }
        }
        return $object;
    }

    /**
     * @param array $array
     * @param string $key
     * @param string $value
     * @return array
     */
    static function search_in_multi($array, $key, $value)
    {
        $results = array();

        if (is_array($array)) {
            if (isset($array[$key]) && $array[$key] == $value) {
                $results[] = $array;
            }

            foreach ($array as $subarray) {
                $results = array_merge($results, self::search_in_multi($subarray, $key, $value));
            }
        }

        return $results;
    }

    /**
     * @param string $formato
     * @param string $fecha
     * @return string
     */
    static function formato_fecha($formato, $fecha)
    {
        try {
            if ($fecha == null) return null;
            $date = date_create_from_format('Y-m-d', $fecha);
            if ($date != false)
                return $date->format($formato);
            else {
                $date = strtotime($fecha);
                return date($formato, $date);
            }
        } catch (Exception $ex) {
            Globales::mensaje_error($ex->getMessage());
        }
    }

    /**
     * @param string $fecha
     * @param string $formato_inicial
     * @param string $formato_final
     * @return string
     */
    static function convertir_formato_fecha($fecha, $formato_inicial, $formato_final)
    {
        $fecha = date_create_from_format($formato_inicial, $fecha);
        $fecha = $fecha->setTimezone(new DateTimeZone('America/Mexico_City'));
        return $fecha->format($formato_final);
    }

    /**
     * @param string $simbolo
     * @param double $cantidad
     * @param float|int $suma
     * @return string
     */
    static function formato_moneda($simbolo, &$cantidad, $suma = 0)
    {
        $cantidad += $suma;
        $cantidad = $simbolo . number_format($cantidad, 2);
        return $cantidad;
    }

    /**
     * @param string $carpeta
     * @param array $archivo
     * @return string
     */
    static function subirImagenSimple($carpeta, $archivo)
    {
        try {
            $debug = print_r($archivo, true);
            if (is_null($archivo)) Globales::mensaje_error("No se subio el archivo " . $debug);
            $name = $_SESSION[token] . "_" . date('YmdHis') . "_" . basename($archivo["name"]);
            if (is_dir($carpeta) && is_writable($carpeta)) {
                if (!move_uploaded_file($archivo['tmp_name'], $carpeta . $name)) {
                    switch ($archivo[error]) {
                        case 1:
                            $max = ini_get('upload_max_filesize');
                            Globales::mensaje_error("El archivo excede el tamaño establecido ($max): " . $debug);
                            break;
                        default:
                            Globales::mensaje_error("Error al subir el archivo ($archivo[tmp_name]) en la ruta: $carpeta$name " . $debug);
                            break;
                    }
                }
            } else {
                Globales::mensaje_error('Upload directory is not writable, or does not exist.');
            }
            return $name;
        } catch (Exception $ex) {
            ini_set("display_errors", 'On');
            ini_set('error_log', 'script_errors.log');
            ini_set('log_errors', 'On');
            error_reporting(E_ALL);
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

            http_response_code(500);
            $msg = $ex->getMessage();
            error_log($msg);
            exit($msg);
        }
    }

    static function setVista()
    {
        if ($_GET[file]) {
            $path = "recursos\\imagenes\\transacciones\\";
            $nombreImagen = self::subirImagenSimple($path, $_FILES["file"]);
            if ($nombreImagen != false)
                echo $nombreImagen;
            exit;
        } else {
            if (isset($_GET[tryit])) {
                if (isset($_SESSION[usuario]))
                    header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
                $json = json_encode($_REQUEST, JSON_FORCE_OBJECT);
                print "<script>getVars = $json;</script>";
            }
            if (self::$modulo != "registro")
                if (isset($_SESSION["usuario"]))
                    self::$modulo = empty($_POST["vista"]) ? self::$modulo : $_POST["vista"];
                else
                    self::$modulo = "login";

            if (isset($_POST["vista"])) {
                if (!empty($_POST["accion"])) $vista = $_POST["accion"];
                elseif (empty($_POST["vista"])) $vista = $_SESSION["modulo"];
                else $vista = $_POST["vista"];

                if (empty($_POST["modulo"]))
                    self::$modulo = $vista;
                if (!empty($_POST["post"])) $_SESSION["post"] = $_POST["post"];
                $_SESSION["modulo"] = self::$modulo;
                exit;
            }
        }
    }

    /**
     * @param string $modulo
     */
    static function setControl($modulo = null)
    {
        if (isset($_GET["aside"])) $modulo = $_REQUEST["asideModulo"] . "/" . $_REQUEST["asideAccion"];
        $control = explode("/", $modulo)[0];

        if (file_exists("controlador/{$control}Control.php") and $_SESSION["namespace"] == self::$namespace)
            require "controlador/{$control}Control.php";
        else {
            session_unset();
            $_SESSION[modulo] = "login";
            $_SESSION["namespace"] = self::$namespace;
            header("Refresh:0");
            exit;
        }

        $namespace = Globales::$namespace;
        $_SESSION["namespace"] = $namespace;
        $clase = "$namespace$control";

        new $clase();
    }

    static function generar_pdf($ruta, $stylesheets, $html)
    {
        if (file_exists($ruta))
            unlink($ruta);
        $html = <<<HTML
$html
<div class="row" style="text-align: center">
    <div class="form-group">
        <div class="col-xs-12 navbar-brand" style="font-size: 12px;">
            <label>
                <img
                        src="recursos/img/logo-smaller.png" alt="">Cbiz Money
            </label>
        </div>
    </div>
</div>
HTML;
        try {
            date_default_timezone_set('America/Mexico_City');
            $mpdf = new mPDF();
            $mpdf->debug = true;
            $mpdf->WriteHTML($stylesheets, 1);
            $mpdf->WriteHTML($html, 2);
            $mpdf->Output($ruta, 'F');
            return true;
        } catch (Exception $ex) {
            echo $ex->getMessage();
            return false;
        }
    }

    function __destruct()
    {
        self::setNamespace("");
    }
}