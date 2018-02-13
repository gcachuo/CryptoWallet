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

    /**
     * Constructor.
     * Carga los recursos, agrega el script del modulo y genera el codigo HTML para la vista.
     * @internal param $vista
     */
    function __construct()
    {
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
            $json = $this->{$_POST["fn"]}();
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
            else {
                $permisos = $this->control->acciones->selectAcciones();
            }
            Globales::setPermisos($permisos);
        }
        return Globales::getPermisos($_SESSION['modulo']);
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

    protected abstract function cargarAside();

    protected abstract function cargarPrincipal();

    private function setVista()
    {
        if (!empty($_POST["accion"])) $vista = $_POST["accion"];
        elseif (empty($_POST["vista"])) $vista = Globales::$modulo;
        else $vista = $_POST["vista"];

        Globales::$modulo = $vista;

        return $vista;
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
     * @param int $padre
     * @param object $modulos
     * @return string
     */
    private function buildModulos($padre, $modulos)
    {
        $idioma = Globales::getIdioma('modulos');
        foreach ($modulos as $modulo) {
            if ($modulo["padreModulo"] == 0) continue;
            /*$permisos = $this->permisosModulo($modulo["idModulo"]);*/
            if ($_SESSION[perfil] != 0) {
                if (!$permisos->accesar and $padre != 0) continue;
            }

            $nombre = (strtolower($idioma->{$modulo["idModulo"]}));
            $navegar = strtolower($modulo["navegarModulo"]);

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
     * Scripts especificos del modulo
     * @param $src
     */
    private function customScript($src)
    {
        $this->customScripts .= <<<HTML
<script type="text/javascript" src="$src"></script>
HTML;

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

    function getApiKeys($pass)
    {
        $api = $this->control->usuario_llaves->selectApiKey($_SESSION['usuario']);
        $api->apiKey = Globales::decrypt($api->apiKey, $pass);
        $api->apiSecret = Globales::decrypt($api->apiSecret, $pass);
        $_SESSION['api_key'] = $api->apiKey;
        $_SESSION['api_secret'] = $api->apiSecret;
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

    function __get($key)
    {
        if ($key == "control") $modulo = $key;
        else {
            $modulo = explode("/", Globales::$modulo)[0];
            //if (isset($_POST["modulo"])) $modulo = $_POST["modulo"];
            if ($modulo != get_class($this)) $modulo = get_class($this);
            elseif ($_GET["aside"]) $modulo = $_REQUEST["asideModulo"];
        }
        $modelo = new ArchivoModelo();
        return $modelo->$modulo;
    }

    /**
     * @param mysqli_result $registros
     * @return array
     */
    protected function obtenerRegistros($registros)
    {
        $datos = [];
        foreach ($registros as $dato) {
            $datos[$dato['id']] = $dato;
            unset($datos[$dato['id']]['id']);
        }
        return $datos;
    }

    protected function buildLista($lista, $default = null, $disallowed = [])
    {
        $html = "";
        foreach ($lista as $key => $item) {
            $disabled = (in_array($key, $disallowed)) ? "disabled" : "";
            $selected = $key == $default ? "selected" : "";
            $html .= <<<HTML
<option $selected $disabled value="$key">$item</option>
HTML;
        }
        return $html;
    }

    /**
     * @param $registros
     * @param array $acciones
     * @param array $columns
     * @return string
     * @throws Exception
     */
    protected function buildTabla($registros, $acciones = [], $columns = [])
    {
        $tabla = "";
        if (get_class($registros) == "mysqli_result") {
            $registros = $this->obtenerRegistros($registros);
        }
        foreach ($registros as $id => $cells) {
            $rows = "";

            $index = 0;
            foreach ($cells as $key => $cell) {
                $explode = explode("-", $columns[$index]);
                $type = $explode[0] ?: $columns[$index]["type"];
                switch ($type) {
                    case "select":
                        $table = $explode[1];
                        $funcion = "selectLista" . ucfirst($table);
                        $registros = $this->modelo->$table->$funcion();
                        $lista = $this->buildLista($registros, $cell);
                        $select = <<<HTML
<select name="$key" id="select$key" data-id="$id">
<option selected disabled value="0">$key</option>
$lista
</select>
HTML;

                        $rows .= <<<HTML
<td>$select</td>
HTML;
                        break;
                    case "date":
                        $cell = Globales::formato_fecha("d/m/Y h:ia", $cell);
                        $rows .= <<<HTML
<td>$cell</td>
HTML;
                        break;
                    case "estatus":
                        $cell = $columns[$index][$cell];
                        $rows .= <<<HTML
<td><a onclick="btnCambiarEstatus($id)" class="label label-lg btn-primary btn">$cell</a></td>
HTML;
                        break;
                    default:
                        $rows .= <<<HTML
<td>$cell</td>
HTML;
                        break;
                }
                $index++;
            }
            $btnAcciones = "";
            foreach ($acciones as $icono => $accion) {
                $btnAcciones .= <<<HTML
<a onclick="btn$accion($id)" title="$accion" class="btn btn-sm btn-default"><i class="material-icons">$icono</i></a>
HTML;
            }
            $rowAcciones = !empty($acciones) ? "<td class='tdAcciones'>$btnAcciones</td>" : "";
            $tabla .= <<<HTML
<tr>
$rows
$rowAcciones
</tr>
HTML;
        }
        return $tabla;
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
}

class ArchivoModelo
{
    function __get($key)
    {
        $namespace = Globales::$namespace;
        $key = strtolower($key);
        $ruta = "modelo/{$key}Modelo.php";
        require_once $ruta;
        $modelo = "Modelo{$key}";
        $class = new $modelo();
        return $class;
    }
}

Class Modelo
{
    static private $token;

    static function clearToken()
    {
        unset($_SESSION[token]);
        self::$token = null;
    }

    public function __toString()
    {
        return get_class($this);
    }

    function __get($key)
    {
        self::getToken();
        $key = ltrim($key, "_");
        $namespace = Globales::$namespace;
        $ruta = HTTP_PATH_ROOT . "modelo/tablas/{$key}.php";
        if (file_exists($ruta)) {
            require_once $ruta;
            $modelo = "{$namespace}Tabla{$key}";
            $tabla = new $modelo(self::$token);
        } else {
            Globales::mensaje_error("No existe el archivo. ($ruta)");
        }
        return $tabla;
    }

    static function getToken()
    {
        $default = Globales::getConfig()->conexion->default_database;
        self::$token = $_SESSION['token'] ?: $default;
        return self::$token;
    }

    static function setToken($token)
    {
        $token = $token ?: self::getToken();
        $_SESSION[token] = $token;
        self::$token = $token;
    }

    public function obtenerCampos($nombre = null)
    {
        $nombre = is_null($nombre) ? $_SESSION['modulo'] : $nombre;
        $idModulo = $this->modulos->selectIdFromNombre($nombre);
        return $this->campos->selectCampos($idModulo);
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