<?php

/**
 * Created by PhpStorm.
 * User: Cachu
 * Date: 23/feb/2017
 * Time: 10:44 AM
 */
class TablaUsuarios extends Tabla
{
    function __construct($token)
    {
        $token = $token ?: ($_SESSION[token]);
        parent::__construct($token);
    }

    function create_table()
    {
        $sql = <<<MySQL
CREATE TABLE `_usuarios`
(
    id_usuario BIGINT(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
    nombre_usuario VARCHAR(100),
    login_usuario VARCHAR(50) NOT NULL,
    password_usuario VARCHAR(255) NOT NULL,
    correo_usuario VARCHAR(255),
    estatus_usuario BIT(1) DEFAULT b'1' NOT NULL,
    perfil_usuario BIGINT(20) DEFAULT '1' NOT NULL,
    id_usuario_create BIGINT(20) NOT NULL COMMENT 'usuario que creo el registro'
);
CREATE UNIQUE INDEX usuarios_login_usuario_uindex ON `_usuarios` (login_usuario);
MySQL;
        return $this->multiconsulta($sql);
    }

    function selectUsuario($login)
    {
        $sql = /** @lang MySQL */
            <<<MySQL
SELECT 
id_usuario idUsuario,
password_usuario passwordUsuario,
perfil_usuario idPerfil,
id_usuario_create idUserCreate
FROM _usuarios
WHERE
  login_usuario = '$login'
 AND estatus_usuario = TRUE 
MySQL;

        $consulta = $this->consulta($sql);
        $registro = $this->siguiente_registro($consulta);
        return $registro;
    }

    function selectUsuarioFromId($id)
    {
        $sql = /** @lang MySQL */
            <<<MySQL
SELECT 
      id_usuario     id,
      nombre_usuario nombre,
      login_usuario  login,
      correo_usuario correo,
      perfil_usuario perfil
FROM _usuarios
WHERE id_usuario=$id
 AND estatus_usuario = TRUE 
MySQL;

        $consulta = $this->consulta($sql);
        $registro = $this->siguiente_registro($consulta);
        return $registro;
    }

    function selectUsuarioFromLogin($login)
    {
        $sql = /** @lang MySQL */
            <<<MySQL
SELECT
  id_usuario        id,
  nombre_usuario    nombreUsuario,
  id_usuario_create idUserCreate,
  estatus_usuario   estatus,
  correo_usuario    email,
  perfil_usuario    perfil
FROM _usuarios
WHERE login_usuario = '$login'
MySQL;

        $consulta = $this->consulta($sql);
        $registro = $this->siguiente_registro($consulta);
        return $registro;
    }

    function selectRegistrosUsuarios()
    {
        $sql = /** @lang MySQL */
            <<<MySQL
SELECT
  id_usuario id,
  nombre_usuario nombre,
  login_usuario  login,
  nombre_perfil  perfil
FROM _usuarios
JOIN _perfiles ON id_perfil=perfil_usuario
WHERE estatus_usuario = TRUE
AND perfil_usuario > 0
MySQL;
        return $this->consulta($sql);
    }

    function insertUsuario($nombre, $login, $password, $correo, $perfil, $usuario = null)
    {
        if (is_null($usuario)) $usuario = $_SESSION["usuario"] ?: 0;
        $sql = /** @lang MySQL */
            <<<MySQL
REPLACE INTO
  _usuarios (nombre_usuario, login_usuario, password_usuario, correo_usuario, perfil_usuario,id_usuario_create)
VALUES ('$nombre', '$login', '$password', '$correo', '$perfil','$usuario')
MySQL;
        return $this->consulta($sql);
    }

    function updateUsuario($id, $nombre, $login, $correo, $perfil, $password)
    {
        if ($password != "") {
            $sql = /**@lang MySQL */
                <<<MySQL
UPDATE _usuarios
SET 
  nombre_usuario = '$nombre',
  login_usuario    = '$login', 
  correo_usuario = '$correo', 
  perfil_usuario = $perfil,
  password_usuario = '$password',
  id_usuario_create = 0
WHERE id_usuario = $id
MySQL;

        } else {
            $sql = /**@lang MySQL */
                <<<MySQL
UPDATE _usuarios
SET 
  nombre_usuario = '$nombre',
  login_usuario    = '$login', 
  correo_usuario = '$correo', 
  perfil_usuario = $perfil
WHERE id_usuario = $id
MySQL;
        }
        $this->consulta($sql);
    }

    function updateEstatusUsuario($id)
    {
        $sql = /**@lang MySQL */
            <<<MySQL
UPDATE _usuarios
SET estatus_usuario = FALSE
WHERE id_usuario = $id
MySQL;
        $this->consulta($sql);
    }

    function selectPerfil($usuario)
    {
        $sql = /** @lang MySQL */
            <<<MySQL
SELECT perfil_usuario perfil
FROM `_usuarios`
WHERE id_usuario = '$usuario'
MySQL;

        $consulta = $this->consulta($sql);
        $registro = $this->siguiente_registro($consulta);
        $perfil = $registro->perfil;
        return $perfil;
    }

    function updateIdUserCreate($id_usuario, $id_usuario_create)
    {
        $sql = <<<MySQL
update _usuarios set id_usuario_create='$id_usuario_create' where id_usuario='$id_usuario';
MySQL;
        $this->consulta($sql);
    }

    public function updateLastLogin($id_usuario, $last_login_usuario)
    {
        $sql = /** @lang MySQL */
            <<<MySQL
UPDATE `_usuarios` SET last_login_usuario='$last_login_usuario' WHERE id_usuario='$id_usuario'
MySQL;
        $this->consulta($sql);
    }
}

function modify_table()
{

}