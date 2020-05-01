<?php


namespace Model;


class Usuarios
{
    public function __construct()
    {
        new Usuarios_Transacciones();
        $mysql = new MySQL();
        $mysql->create_table('usuarios', [
            new TableColumn('id_usuario', ColumnTypes::BIGINT, 20, true, null, true, true),
            new TableColumn('id_cliente', ColumnTypes::BIGINT, 20, false),
            new TableColumn('perfil_usuario', ColumnTypes::INTEGER, 11, false, 1),
            new TableColumn('nombre_usuario', ColumnTypes::VARCHAR, 100, true),
            new TableColumn('correo_usuario', ColumnTypes::VARCHAR, 100, true),
            new TableColumn('password_usuario', ColumnTypes::VARCHAR, 255, true),
            new TableColumn('last_login_usuario', ColumnTypes::DATETIME, 0, false),
        ], <<<sql
ALTER TABLE usuarios
	ADD CONSTRAINT usuarios_usuarios_id_usuario_fk
		FOREIGN KEY (id_cliente) REFERENCES usuarios (id_usuario)
			ON UPDATE CASCADE ON DELETE SET NULL;
CREATE UNIQUE INDEX usuarios_correo_usuario_uindex ON usuarios (correo_usuario);
sql
        );
    }

    public function selectPassword($email)
    {
        $sql = <<<sql
SELECT password_usuario password FROM usuarios WHERE correo_usuario=?
sql;
        $mysql = new MySQL();
        return $mysql->fetch_single($mysql->prepare($sql, ['s', $email]))['password'];
    }

    function selectUser($email)
    {
        $sql = <<<sql
SELECT id_usuario id, nombre_usuario nombre, correo_usuario correo, perfil_usuario perfil
FROM usuarios
WHERE correo_usuario=?
sql;
        $mysql = new MySQL();
        return $mysql->fetch_single($mysql->prepare($sql, ['s', $email]));
    }

    public function updateLastLogin($user_id)
    {
        $sql = <<<sql
UPDATE usuarios SET last_login_usuario=NOW() WHERE id_usuario=?
sql;
        $mysql = new MySQL();
        $mysql->prepare($sql, ['i', $user_id]);
    }

    function selectClients($user_id)
    {
        $sql = <<<sql
SELECT
  u.id_usuario id,
  nombre_usuario nombre,
  m.id_moneda                            idMoneda,
  nombre_moneda                          moneda,
  sum(costo_usuario_moneda)              costo,
  round(sum(cantidad_usuario_moneda), 8) cantidad,
  concat(ut.id_moneda, '_', par_moneda)  book
FROM usuarios u
       INNER JOIN usuarios_transacciones ut ON u.id_usuario = ut.id_usuario
       INNER JOIN monedas m ON ut.id_moneda = m.id_moneda
WHERE id_cliente = ?
GROUP BY u.id_usuario,m.id_moneda;
sql;
        $mysql = new MySQL();
        return $mysql->fetch_all($mysql->prepare($sql, ['i', $user_id]));
    }

    public function insertUsuario($name, $email, $password)
    {
        $sql = <<<sql
INSERT INTO usuarios(nombre_usuario, correo_usuario, password_usuario)
VALUES (:name, :email, :password)
ON DUPLICATE KEY UPDATE nombre_usuario=:name,
                        password_usuario=:password;
sql;
        $mysql = new MySQL();
        $mysql->prepare2($sql, [
            ':name' => $name,
            ':email' => $email,
            ':password' => password_hash($password, CRYPT_BLOWFISH)
        ]);
    }
}
