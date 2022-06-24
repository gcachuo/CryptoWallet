<?php

namespace Model;

use CoreException;
use Model\Enum\Notification_Types;
use System;

class Usuarios_Notificaciones
{
    /**
     * @throws CoreException
     */
    public function __construct()
    {
        $mysql = new MySQL();
        $mysql->create_table("usuarios_notificaciones", [
            new TableColumn('id_usuario_notificacion', ColumnTypes::BIGINT, 20, true, null, true, true),
            new TableColumn('id_usuario', ColumnTypes::BIGINT, 20, true),
            new TableColumn('type', ColumnTypes::VARCHAR, 15, true),
            new TableColumn('message', ColumnTypes::VARCHAR, 100, true),
            new TableColumn('data', ColumnTypes::JSON, 0, false),
            new TableColumn('timestamp', ColumnTypes::TIMESTAMP, 0, false, 'current_timestamp'),
        ]);
    }

    /**
     * @throws CoreException
     */
    public static function BITSO_ERROR(string $ticker_error): void
    {
        $user = System::decode_token(USER_TOKEN);
        $user_id = $user['id'];
        $user_id = System::decrypt($user_id);

        $usuarios_notificaciones = new Usuarios_Notificaciones();
        $usuarios_notificaciones->insertRow($user_id, Notification_Types::$error, 'Bitso: ' . $ticker_error);
    }

    /**
     * @throws CoreException
     */
    public static function LIMITE_COMPRA(array $moneda, float $limit, float $porcentaje): void
    {
        $user = System::decode_token(USER_TOKEN);
        $user_id = $user['id'];
        $user_id = System::decrypt($user_id);

        $usuarios_notificaciones = new Usuarios_Notificaciones();
        $last = $usuarios_notificaciones->selectLast($user_id, $moneda['idMoneda']);

        if ($last['type'] == Notification_Types::$activity_down) {
            $usuarios_transacciones = new Usuarios_Transacciones();
            ["diff" => $diff] = $usuarios_transacciones->selectDiff(date('Y-m-d H:i', strtotime($last['timestamp'])), $user_id, $moneda['idMoneda']);

            if ($diff > 0) {
                return;
            }
        }

        $limit = $limit * 100;
        $porcentaje = round($porcentaje * 100, 2);
        $usuarios_notificaciones->insertRow($user_id, Notification_Types::$activity_down, "$moneda[moneda] ha bajado más del $limit%. Porcentaje actual: $porcentaje%", [
            'coin' => $moneda['idMoneda']
        ]);
    }

    /**
     * @throws CoreException
     */
    public static function LIMITE_VENTA(array $moneda, float $limit, float $porcentaje): void
    {
        $user = System::decode_token(USER_TOKEN);
        $user_id = $user['id'];
        $user_id = System::decrypt($user_id);

        $usuarios_notificaciones = new Usuarios_Notificaciones();
        $last = $usuarios_notificaciones->selectLast($user_id, $moneda['idMoneda']);

        if ($last['type'] == Notification_Types::$activity_up) {
            $usuarios_transacciones = new Usuarios_Transacciones();
            ['diff' => $diff] = $usuarios_transacciones->selectDiff(date('Y-m-d H:i', strtotime($last['timestamp'])), $user_id, $moneda['idMoneda']);

            if ($diff > 0) {
                return;
            }
        }

        $limit = $limit * 100;
        $porcentaje = round($porcentaje * 100, 2);
        $usuarios_notificaciones->insertRow($user_id, Notification_Types::$activity_up, "$moneda[moneda] ha subido más del $limit%. Porcentaje actual: $porcentaje%", [
            'coin' => $moneda['idMoneda']
        ]);
    }

    /**
     * @return string[][]
     * @throws CoreException
     */
    public function selectRows(int $id_usuario): array
    {
        $sql = <<<sql
SELECT * FROM usuarios_notificaciones WHERE id_usuario = :id_usuario ORDER BY timestamp DESC;
sql;

        $mysql = new MySQL();
        return $mysql->prepare2($sql, [
            ':id_usuario' => $id_usuario
        ])->fetchAll();
    }

    /**
     * @throws CoreException
     */
    private function insertRow(int $id_usuario, string $type, string $message, array $data = null): void
    {
        $sql = <<<sql
INSERT INTO usuarios_notificaciones(id_usuario, type, message, data) VALUES (:id_usuario, :type, :message, :data);
sql;
        $mysql = new MySQL();
        $mysql->prepare2($sql, [
            ':id_usuario' => $id_usuario,
            ':type' => $type,
            ':message' => $message,
            ':data' => $data,
        ]);
    }

    /**
     * @throws CoreException
     */
    private function selectLast(string $user_id, string $idMoneda)
    {
        $sql = <<<sql
SELECT *
FROM usuarios_notificaciones
WHERE
	  JSON_EXTRACT(data, '$.coin') = :id_moneda
  AND id_usuario = :id_usuario
ORDER BY
	timestamp DESC
LIMIT 1;
sql;
        $mysql = new MySQL();
        return $mysql->prepare2($sql, [
            ':id_moneda' => $idMoneda,
            ':id_usuario' => $user_id
        ])->fetch();
    }
}
