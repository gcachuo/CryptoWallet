<?php

/**
 * Created by PhpStorm.
 * User: Cachu
 * Date: 20/feb/2017
 * Time: 12:43 PM
 */

/**
 * Class Control
 * @property ModeloControl control
 */
abstract class Control
{
    /** @var Globales $idioma */
    public $idioma;

    public $permisos, $nombreUsuario, $vista, $error, $modulos, $tabla, $diasRestantes, $listNotifications, $numNot;

    private $customStylesheets, $customScripts, $stylesheets, $scripts, $page;

    protected abstract function cargarPrincipal();

    protected abstract function cargarAside();

    /**
     * Constructor.
     * Carga los recursos, agrega el script del modulo y genera el codigo HTML para la vista.
     * @internal param $vista
     */
    function __construct()
    {
        $this->obtenerDiasRestantes();
        $this->buildListNotificacions();
        $this->obtenerIdioma();
        $this->permisos = $this->permisosModulo();
        $this->nombreUsuario = $this->obtenerNombreUsuario();
        if (isset($_POST["form"])) {
            if (empty($_POST[aside])) {
                parse_str($_POST["form"], $_POST["form"]);
                $_POST = array_merge($_POST, $_POST["form"]);
                unset($_POST["form"]);
            } else {
                unset($_POST["form"]);
                parse_str($_POST["aside"], $_POST["aside"]);
                $_POST = array_merge($_POST, $_POST["aside"]);
                unset($_POST["aside"]);
            }
        }
        if (isset($_POST["post"])) {
            $_POST += $_POST["post"];
            /*if (isset($_POST["modo"]))
                $data = $this->$_POST["modo"]();*/
        }
        if (isset($_POST["fn"])) {
            $json = $this->$_POST["fn"]();
            echo json_encode($json);
        } else {
            if (isset($_SESSION["post"]) and empty($_POST["post"])) $_POST["post"] = $_SESSION["post"];
            unset($_SESSION["post"]);

            if (isset($_GET["aside"]) or strpos($_SESSION["modulo"], "/")) $this->cargarAside();
            else $this->cargarPrincipal();

            $vista = $this->setVista();
            $this->getAssets();

            if ($vista != "login" and $vista != "registro") {
                $modulos = $this->control->obtenerModulos();
                $this->modulos = $this->buildModulos(0, $modulos);
            }
            if (isset($_GET["aside"])) {
                $vista = $_REQUEST["asideModulo"] . "/" . $_REQUEST["asideAccion"];
                $file = $_REQUEST["asideModulo"] . "_" . $_REQUEST["asideAccion"];
                if (file_exists("recursos/css/{$file}.css"))
                    $this->customStylesheet("recursos/css/{$file}.css");
                if (file_exists("recursos/js/{$file}.js"))
                    $this->customScript("recursos/js/{$file}.js");
                $page = $this->buildAside($vista, $data) . $this->customStylesheets . $this->customScripts;
            } else {
                $page = $this->buildPage($vista, $data);
            }
            echo $page;
            $this->showMessage();
        }
    }

    function showMessage()
    {
        if (isset($_SESSION[messages])) {
            $message = "";
            $color = "";
            switch (true) {
                case $_SESSION[messages][transaccion]:
                    $message = "Registrado correctamente";
                    $color = "light-green-500";
                    break;
            }
            echo "<script>showMessage('$message', '$color');</script>";
            unset($_SESSION[messages]);
        }
    }

    function __get($key)
    {
        if ($key == "control") $modulo = $key;
        else {
            $modulo = explode("/", Globales::$modulo)[0];
            if (isset($_POST["modulo"])) $modulo = $_POST["modulo"];
            if ($_GET["aside"]) $modulo = $_REQUEST["asideModulo"];
        }
        $modelo = new Modelo();
        return $modelo->$modulo;
    }

    private function setVista()
    {
        if (!empty($_POST["accion"])) $vista = $_POST["accion"];
        elseif (empty($_POST["vista"])) $vista = Globales::$modulo;
        else $vista = $_POST["vista"];

        Globales::$modulo = $vista;

        return $vista;
    }

    /**
     * @param int $padre
     * @param object $modulos
     * @return string
     */
    private function buildModulos($padre, $modulos)
    {
        if ($this->diasRestantes == 0)
            return null;
        $idioma = Globales::getIdioma('modulos');
        foreach ($modulos as $modulo) {
            if ($modulo["padreModulo"] == 0) continue;
            /*$permisos = $this->permisosModulo($modulo["idModulo"]);*/
            if ($_SESSION[perfil] != 0) {
                if (!$permisos->accesar and $padre != 0) continue;
            }

            $nombre = mb_strtolower($idioma->$modulo["idModulo"]);
            $navegar = mb_strtolower($modulo["navegarModulo"]);

            if ($modulo["iconoModulo"] != "")
                $icono = <<<HTML
        <span class="nav-icon"><i class="material-icons">$modulo[iconoModulo]</i></span>
HTML;

            if ($submodulos == null) {
                if ($navegar != "") $onclick = "onclick=\"navegar('$navegar');\"";
            }
            $htmlModulos .= <<<HTML
<li class="nav-header hidden-folded">
        $icono
    <a $onclick>
        <span class="nav-text">$nombre</span>
    </a>
</li>
HTML;
        }
        return $htmlModulos;
    }

    /**
     * @return object
     * @internal param string $modulo
     */
    private function permisosModulo()
    {
        $nombreModulo = $_SESSION[modulo];
        if ($nombreModulo != null) {
            if ($_SESSION[perfil] != 0)
                $permisos = $this->control->obtenerPermisosModulo();
            Globales::setPermisos($permisos);
        }
        return Globales::getPermisos($_SESSION['modulo']);
    }

    /**
     * Genera la vista en HTML a partir del controlador
     * @param $vista
     * @return string
     * @throws Exception
     */
    private function buildPage($vista, $data)
    {
        $version = Globales::$version;

        ob_start();
        if (!file_exists("vista/{$vista}.phtml")) $vista = "404";
        require "vista/{$vista}.phtml";
        $this->page = ob_get_contents();
        ob_end_clean();
        require "vista/wrap.phtml";
        $pagina = ob_get_contents();
        ob_end_clean();
        return $pagina;
    }

    private function buildAside($vista, $data)
    {
        ob_start();
        if (!file_exists("vista/{$vista}.phtml")) $vista = "404";
        require_once "vista/{$vista}.phtml";
        $pagina = ob_get_contents();
        ob_end_clean();
        return $pagina;
    }

    /**
     * Condensa en HTML los recursos requeridos, como plugins
     * @return string
     */
    private function getAssets()
    {
        $modulo = str_replace("/", "_", Globales::$modulo);

        $plugins = HTTP_PATH_ROOT . "recursos/plugins";
        $js = HTTP_PATH_ROOT . "recursos/js";
        $css = HTTP_PATH_ROOT . "recursos/css";

        $assets = "$plugins/flatkit/assets";
        $libs = "$plugins/flatkit/libs";
        $scripts = "$plugins/flatkit/scripts";

        $this->stylesheet("$assets/animate.css/animate.min.css");
        $this->stylesheet("$assets/glyphicons/glyphicons.css");
        $this->stylesheet("//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css");
        $this->stylesheet("$assets/material-design-icons/material-design-icons.css");

        $this->stylesheet("$assets/bootstrap/dist/css/bootstrap.css");
        $this->minStylesheet("$assets/styles/app.css", "$assets/styles/app.min.css");
        $this->stylesheet("$assets/styles/font.css");

        $this->script("$libs/jquery/jquery/dist/jquery.js");
        $this->script("$libs/jquery/tether/dist/js/tether.min.js");
        $this->script("$libs/jquery/bootstrap/dist/js/bootstrap.js");
        $this->script("$libs/jquery/underscore/underscore-min.js");
        $this->script("$libs/jquery/jQuery-Storage-API/jquery.storageapi.min.js");
        #$this->script("$libs/jquery/PACE/pace.min.js");
        $this->script("$plugins/daterangepicker/moment.js");

        $this->stylesheet("$plugins/daterangepicker/daterangepicker.css");
        $this->script("$plugins/daterangepicker/daterangepicker.js");

        $this->script("$scripts/config.lazyload.js");

        $this->script("$scripts/palette.js");
        $this->script("$scripts/ui-load.js");
        $this->script("$scripts/ui-jp.js");
        $this->script("$scripts/ui-include.js");
        $this->script("$scripts/ui-device.js");
        $this->script("$scripts/ui-form.js");
        #$this->script("$scripts/ui-nav.js");
        $this->script("$scripts/ui-screenfull.js");
        $this->script("$scripts/ui-scroll-to.js");
        $this->script("$scripts/ui-toggle-class.js");
        $this->script("$scripts/app.js");

        #$this->script("$libs/jquery/jquery-pjax/jquery.pjax.js");
        $this->script("$scripts/ajax.js");

        $this->stylesheet("$libs/jquery/plugins/integration/bootstrap/3/dataTables.bootstrap.css");
        $this->script("$plugins/datatables/datatables.js");

        $this->stylesheet("$plugins/select2/css/select2.css");
        $this->script("$plugins/select2/js/select2.full.js");

        $this->stylesheet("$plugins/dropzone/dropzone.css");
        //$this->stylesheet("$plugins/dropzone/style.css");
        $this->script("$plugins/dropzone/dropzone.js");

        $this->stylesheet("$plugins/nestable/jquery.nestable.css");
        $this->script("$plugins/nestable/jquery.nestable.js");

        $this->stylesheet("$plugins/switchery/dist/switchery.css");
        $this->script("$plugins/switchery/dist/switchery.js");

        $this->script("$plugins/echarts/build/dist/theme.js");
        $this->script("$plugins/echarts/build/dist/echarts-all.js");
        $this->script("$plugins/echarts/build/dist/jquery.echarts.js");
        $this->script("$plugins/echarts/build/dist/echarts.js");

        $this->script("$plugins/maskedinput/masked-input-1.4-min.js");
        $this->script("$plugins/jic/js/JIC.js");

        #Override
        $this->stylesheet(HTTP_PATH_ROOT . "recursos/css/wrap.css");
        $this->stylesheet("$css/styles.css");
        $this->script("$js/globales.js");

        if (file_exists("recursos/css/{$modulo}.css"))
            $this->stylesheet("recursos/css/{$modulo}.css");
        if (file_exists("recursos/js/{$modulo}.js"))
            $this->script("recursos/js/{$modulo}.js");

        $this->stylesheets .= $this->customStylesheets;
        $this->scripts .= $this->customScripts;
    }

    /**
     * Convierte el enlace del recurso en etiquetas de referencia para CSS
     * @param $href
     */
    private function stylesheet($href)
    {
        $this->stylesheets .= <<<HTML
<link rel="stylesheet" type="text/css" href="$href">
HTML;
    }

    private function minStylesheet($href, $hrefmin)
    {
        $this->stylesheets .= <<<HTML
  <!-- build:css $hrefmin -->
<link rel="stylesheet" type="text/css" href="$href">
  <!-- endbuild -->
HTML;
    }

    /**
     * Hojas de estilo especificas del modulo
     * @param $href
     */
    private function customStylesheet($href)
    {
        $this->customStylesheets .= <<<HTML
<link rel="stylesheet" type="text/css" href="$href">
HTML;
    }

    /**
     * Convierte el enlace del recurso en etiquetas de referencia para Javascript
     * @param $src
     */
    private function script($src)
    {
        $this->scripts .= <<<HTML
<script src="$src"></script>
HTML;

    }

    /**
     * Scripts especificos del modulo
     * @param $src
     */
    private function customScript($src)
    {
        $this->customScripts .= <<<HTML
<script type="text/javascript" src="$src"></script>
HTML;

    }

    /**
     * @property $formatoFecha
     * @return array
     */
    private function obtenerIdioma()
    {
        if (!empty($_GET["lang"])) $selectIdioma = $_GET["lang"];
        elseif (!empty($_SESSION["idioma"])) $selectIdioma = $_SESSION["idioma"];
        else $selectIdioma = "es_mx";

        $_SESSION["idioma"] = $selectIdioma;

        $json = file_get_contents(HTTP_PATH_ROOT . "recursos/lang/$selectIdioma.json");
        $idioma = json_decode($json, false);

        $modulo = Globales::$modulo;
        if ($_GET[aside])
            $modulo = $modulo . "/" . $_POST[asideAccion];
        Globales::setIdioma($idioma);
        $this->idioma = $idioma->$modulo;
        return (array)$idioma;
    }

    protected function buildListaEstados()
    {
        $estados = $this->control->obtenerEstados();

        foreach ($estados as $estado) {
            $listaEstados .= <<<HTML
<option value="$estado[idEstado]">$estado[nombreEstado]</option>
HTML;
        }

        return $listaEstados;
    }

    protected function buildListaCiudades()
    {
        $listaCiudades = <<<HTML
<option></option>
HTML;

        $ciudades = $this->control->obtenerCiudades($_POST["estado"]);

        foreach ($ciudades as $ciudad) {
            $listaCiudades .= <<<HTML
<option value="$ciudad[idCiudad]">$ciudad[nombreCiudad]</option>
HTML;
        }

        return compact('listaCiudades');
    }

    function obtenerNombreUsuario()
    {
        $usuario = new stdClass();
        $namespace = Globales::$namespace;
        if (isset($_SESSION[usuario]))
            if ($namespace == "\\")
                $usuario = $this->control->usuarios->selectUsuarioFromId($_SESSION[usuario]);
            else
                $usuario = $_SESSION[usuario];
        return $usuario->nombre;
    }

    private function obtenerDiasRestantes()
    {
        if (isset($_SESSION[usuario])) {
            $dias = $this->control->obtenerDiasRestantes($_SESSION[usuario]);
            $this->diasRestantes = $dias;
            if ($dias == 0) {
                if (isset($_POST[fn]))
                    exit('Ya no tiene dias restantes');
                $_SESSION[modulo] = "pago";
                //print "<script>location.reload(true);</script>";
            }
        }
    }

    function buildListNotificacions()
    {
        $this->numNot = 0;
        if ($this->diasRestantes != -1) {
            $this->numNot++;
            $this->listNotifications .= <<<HTML
    <div class="scrollable" style="max-height: 220px">
        <ul class="list-group list-group-gap m-a-0">
            <li class="list-group-item black lt box-shadow-z0 b">
                <a onclick="navegar('pago')">Te quedan $this->diasRestantes días de la version
                    de
                    prueba</a>
            </li>
        </ul>
    </div>
HTML;
        }
    }
}

class Modelo
{
    function __get($key)
    {
        $namespace = Globales::$namespace;
        $ruta = HTTP_PATH_ROOT . "{$namespace}modelo/{$key}Modelo.php";
        if (!file_exists($ruta)) {
            $ruta = HTTP_PATH_ROOT . "modelo/{$key}Modelo.php";
            $namespace = "";
        };
        if (file_exists($ruta)) {
            require_once $ruta;
            $modelo = "{$namespace}Modelo{$key}";
            $class = new $modelo();
        }
        return $class;
    }
}

Class Tabla
{
    static private $token;

    static function setToken($token)
    {
        $_SESSION[token] = $token;
        self::$token = $token;
    }

    function __get($key)
    {
        $namespace = Globales::$namespace;
        $ruta = HTTP_PATH_ROOT . "modelo/tablas/{$namespace}{$key}.php";
        if (file_exists($ruta)) {
            require_once $ruta;
            $modelo = "{$namespace}Tabla{$key}";
            $tabla = new $modelo(self::$token);
        } else {
            Globales::mensaje_error("No existe el archivo. ($ruta)");
        }
        return $tabla;
    }

    protected function obtenerNombrePadre($idPadre)
    {
        switch ($idPadre) {
            case "I":
                $nombrePadre = "Ingresos";
                break;
            case "E":
                $nombrePadre = "Gastos";
                break;
            case "P":
                $nombrePadre = "Productos";
                break;
            case "S":
                $nombrePadre = "Servicios";
                break;
            default:
                $nombrePadre = "";
                break;
        }
        return $nombrePadre;
    }
}