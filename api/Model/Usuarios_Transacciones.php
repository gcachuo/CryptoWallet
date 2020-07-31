<?php


namespace Model;


use BitsoAPI\bitso;
use Helper\BitsoOrderPayload;

class Usuarios_Transacciones
{
    public function __construct()
    {
        new Monedas();
        $mysql = new MySQL();
        $mysql->create_table('usuarios_transacciones', [
            new TableColumn('id_usuario_transaccion', ColumnTypes::BIGINT, 20, true, null, true, true),
            new TableColumn('id_usuario', ColumnTypes::BIGINT, 20, true),
            new TableColumn('id_moneda', ColumnTypes::VARCHAR, 5, true),
            new TableColumn('costo_usuario_moneda', ColumnTypes::DECIMAL, "15,2", false, "0.00"),
            new TableColumn('cantidad_usuario_moneda', ColumnTypes::DECIMAL, "15,8", true),
            new TableColumn('precio_original_usuario_moneda', ColumnTypes::DECIMAL, "15,2", false),
            new TableColumn('precio_real_usuario_moneda', ColumnTypes::DECIMAL, "15,2", false),
            new TableColumn('fecha_usuario_transaccion', ColumnTypes::TIMESTAMP, 0, false, "CURRENT_TIMESTAMP"),
        ], <<<sql
ALTER TABLE usuarios_transacciones
	ADD CONSTRAINT usuarios_transacciones_monedas_id_moneda_fk
		FOREIGN KEY (id_moneda) REFERENCES monedas (id_moneda)
			ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE usuarios_transacciones
	ADD CONSTRAINT usuarios_transacciones_usuarios_id_usuario_fk
		FOREIGN KEY (id_usuario) REFERENCES usuarios (id_usuario)
			ON UPDATE CASCADE ON DELETE CASCADE;
sql
        );
    }

    function selectAmounts($user_id)
    {
        $sql = <<<sql
SELECT
  m.id_moneda idMoneda,
  nombre_moneda moneda,
  sum(costo_usuario_moneda)              costo,
  round(sum(cantidad_usuario_moneda), 8) cantidad,
  concat(ut.id_moneda,'_',par_moneda) book
FROM usuarios_transacciones ut
INNER JOIN monedas m ON ut.id_moneda = m.id_moneda
WHERE id_usuario = ?
GROUP BY ut.id_moneda;
sql;

        $mysql = new MySQL();
        return $mysql->fetch_all($mysql->prepare($sql, ['i', $user_id]));
    }

    function selectDiff($fecha, $user_id, $id_moneda)
    {
        $sql = <<<sql
SELECT fecha_usuario_transaccion old,? new,TIMESTAMPDIFF(MINUTE,fecha_usuario_transaccion,?) diff FROM usuarios_transacciones
WHERE id_usuario=? AND id_moneda=? ORDER BY id_usuario_transaccion DESC
LIMIT 1;
sql;
        $mysql = new MySQL();
        return $mysql->fetch_single($mysql->prepare($sql, ['ssis', $fecha, $fecha, $user_id, $id_moneda]));
    }

    function insertTrade($user_id, $id_moneda, $costo, $cantidad, $income = true)
    {
        if (!$income) {
            $costo = -$costo;
            $cantidad = -$cantidad;
        }

        $sql = <<<sql
INSERT INTO usuarios_transacciones(id_usuario, id_moneda, costo_usuario_moneda, cantidad_usuario_moneda)
VALUES (:id_usuario, :id_moneda, :costo_usuario_moneda, :cantidad_usuario_moneda);
sql;
        $mysql = new MySQL();
        $mysql->prepare2($sql, [
            ':id_usuario' => $user_id,
            ':id_moneda' => $id_moneda,
            ':costo_usuario_moneda' => $costo ?: '0',
            ':cantidad_usuario_moneda' => $cantidad
        ]);
    }

    /**
     * @param $user_id
     * @param $id_moneda
     * @param $costo
     * @param BitsoOrderPayload $order
     */
    function insertOrder($user_id, $id_moneda, $costo, &$order)
    {
        if (empty($order->original_amount)) {
            $bitso = new bitso('', '');
            $ticker = $bitso->ticker(["book" => $order->book]);
            $order->original_amount = $order->original_value / round($ticker->payload->bid, 2);
        }
        $sql = <<<sql
INSERT INTO usuarios_transacciones(id_usuario, id_moneda, costo_usuario_moneda, cantidad_usuario_moneda)
VALUES (:id_usuario, :id_moneda, :costo_usuario_moneda, :cantidad_usuario_moneda);
sql;
        $mysql = new MySQL();
        $mysql->prepare2($sql, [
            ':id_usuario' => $user_id,
            ':id_moneda' => $id_moneda,
            ':costo_usuario_moneda' => -$costo,
            ':cantidad_usuario_moneda' => -$order->original_amount
        ]);

        $sql = <<<sql
INSERT INTO usuarios_transacciones(id_usuario, id_moneda, costo_usuario_moneda, cantidad_usuario_moneda)
VALUES (:id_usuario, 'mxn', :costo_usuario_moneda, :cantidad_usuario_moneda);
sql;
        $mysql->prepare2($sql, [
            ':id_usuario' => $user_id,
            ':costo_usuario_moneda' => $costo,
            ':cantidad_usuario_moneda' => $costo
        ]);

        $this->setPrice();
    }

    function setPrice()
    {
        $sql = <<<sql
UPDATE usuarios_transacciones
SET precio_real_usuario_moneda     = if(id_moneda = 'mxn', 1,
                                        if(cantidad_usuario_moneda != 0, costo_usuario_moneda / cantidad_usuario_moneda,
                                           0))
WHERE precio_real_usuario_moneda IS NULL;
sql;
        $mysql = new MySQL();
        $mysql->prepare2($sql);
    }

    public function selectTrades()
    {
        $this->setPrice();

        $sql = <<<sql
SELECT 
    fecha_usuario_transaccion date,
    costo_usuario_moneda cost,
    cantidad_usuario_moneda quantity,
    precio_real_usuario_moneda price
FROM usuarios_transacciones
WHERE
	  id_usuario = 1
  AND id_moneda = 'bch'
order by fecha_usuario_transaccion;
sql;
        $mysql = new MySQL();
        $query = $mysql->prepare2($sql);
        return $query->fetchAll();
    }
}
