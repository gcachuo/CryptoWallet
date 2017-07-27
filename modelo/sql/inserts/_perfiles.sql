INSERT INTO `_perfiles` (id_perfil, id_usuario_create, nombre_perfil, estatus_perfil)
VALUES (0, 0, 'SuperUsuario', TRUE);
UPDATE `_perfiles`
SET id_perfil = 0
WHERE nombre_perfil = 'SuperUsuario';
INSERT INTO `_perfiles` (id_perfil, id_usuario_create, nombre_perfil, estatus_perfil)
VALUES (1, 0, 'Administrador', TRUE);