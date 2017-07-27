<?php

/**
 * Created by PhpStorm.
 * User: Cachu
 * Date: 28/feb/2017
 * Time: 01:34 PM
 */
class TablaPerfiles_Acciones extends bd
{
    function create_table()
    {
        // TODO: Implement create_table() method.
    }

    function selectAccionesFromModuloAndPerfil($modulo, $perfil)
    {
        $sql = /** @lang MySQL */
            <<<MySQL
SELECT
  descripcion_accion                      accion,
  coalesce(pa.id_perfil = p.id_perfil, 0) estatus
FROM perfiles_acciones pa
  RIGHT JOIN _acciones a ON a.id_accion = pa.id_accion
  JOIN _perfiles p ON p.id_perfil = pa.id_perfil
WHERE pa.id_perfil = $perfil AND id_modulo = $modulo;
MySQL;
        return $this->consulta($sql);
    }

    function selectAccionesFromPerfil($id_perfil)
    {
        $sql = /** @lang */
            <<<MySQL
SELECT
  navegar_modulo                          modulo,
  descripcion_accion                      accion,
  coalesce(pa.id_perfil = p.id_perfil, 0) estatus
FROM perfiles_acciones pa
  RIGHT JOIN _acciones a ON a.id_accion = pa.id_accion
  JOIN _perfiles p ON p.id_perfil = pa.id_perfil
  inner join _modulos m on m.id_modulo=pa.id_modulo
WHERE pa.id_perfil = '$id_perfil';
MySQL;
        $consulta = $this->consulta($sql);
        $acciones = $this->query2multiarray($consulta);
        return $acciones;
    }

    function insertPerfilAccion($idPerfil, $idAccion, $idModulo)
    {
        $sql = /**@lang MySQL */
            <<<MySQL
REPLACE INTO perfiles_acciones (id_perfil, id_accion, id_modulo) 
VALUES ($idPerfil, $idAccion, $idModulo);
MySQL;
        $this->consulta($sql);
    }

    function deleteAccionesPerfil($id_perfil)
    {
        $sql = /** @lang MySQL */
            <<<MySQL
DELETE FROM perfiles_acciones WHERE id_perfil=$id_perfil;
MySQL;
        $this->consulta($sql);
    }
}