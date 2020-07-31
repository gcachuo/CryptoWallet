<?php


namespace Model;


class Usuarios_Keys
{
    function selectKeys($user_id)
    {
        $sql = <<<sql
SELECT api_key, api_secret
FROM usuarios_keys
WHERE id_usuario = :id_usuario;
sql;
        $mysql = new MySQL();
        return $mysql->prepare2($sql, [
            ':id_usuario' => $user_id
        ])->fetch();
    }

    public function insertKey($user_id, string $api_key, string $api_secret)
    {
        $sql = <<<sql
INSERT IGNORE INTO crypto.usuarios_keys(id_usuario, api_key, api_secret) VALUES (:id_usuario, :api_key, :api_secret)
sql;
        $mysql = new MySQL();
        return $mysql->prepare2($sql, [
            ':id_usuario' => $user_id,
            ':api_key' => $api_key,
            ':api_secret' => $api_secret,
        ]);
    }
}
