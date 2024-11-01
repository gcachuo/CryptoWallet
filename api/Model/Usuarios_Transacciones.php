<?php


namespace Model;


use BitsoAPI\bitso;
use CoreException;
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
            new TableColumn('oid', ColumnTypes::VARCHAR, 100, false),
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
  SUM(costo_usuario_moneda)              costo,
  ROUND(SUM(cantidad_usuario_moneda), 8) cantidad,
  CONCAT(ut.id_moneda,'_',par_moneda) book
FROM usuarios_transacciones ut
INNER JOIN monedas m ON ut.id_moneda = m.id_moneda
WHERE id_usuario = :id_usuario
GROUP BY ut.id_moneda;
sql;

        $mysql = new MySQL();
        return $mysql->prepare2($sql, [':id_usuario' => $user_id])->fetchAll();
    }

    function selectBuyPriceAvg(int $id_usuario)
    {
        $sql = <<<sql
SELECT id_moneda,
      AVG(costo_usuario_moneda / cantidad_usuario_moneda) precio_compra_promedio
FROM usuarios_transacciones
WHERE id_usuario = :id_usuario
AND costo_usuario_moneda / cantidad_usuario_moneda>0
AND costo_usuario_moneda>0
GROUP BY id_moneda
sql;
        $mysql = new MySQL();
        return array_column($mysql->prepare2($sql, [':id_usuario' => $id_usuario])->fetchAll(), 'precio_compra_promedio', 'id_moneda');
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

    /**
     * @param $user_id
     * @param $id_moneda
     * @param $costo
     * @param $cantidad
     * @param $income
     * @return void
     * @throws CoreException
     */
    function insertTrade($user_id, $id_moneda, $costo, $cantidad, $income = true): void
    {
        $costo = abs($costo);
        $cantidad = abs($cantidad);

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
     * @param int $user_id
     * @param string $order_id
     * @throws CoreException
     */
    function insertOrder(int $user_id, string $order_id)
    {
        $bitso = new \Helper\Bitso($user_id);
        $trade = $bitso->orderTrades($order_id);

        $sql = <<<sql
INSERT INTO usuarios_transacciones(id_usuario, id_moneda, costo_usuario_moneda, cantidad_usuario_moneda, precio_original_usuario_moneda, oid)
VALUES (:id_usuario, :id_moneda, :costo_usuario_moneda, :cantidad_usuario_moneda,:precio_original_usuario_moneda, :oid);
sql;
        $mysql = new MySQL();
        $mysql->prepare2($sql, [
            ':id_usuario' => $user_id,
            ':id_moneda' => $trade->major_currency,
            ':costo_usuario_moneda' => -($trade->minor - $trade->fees_amount),
            ':cantidad_usuario_moneda' => $trade->major,
            ':precio_original_usuario_moneda' => $trade->price,
            ':oid' => $trade->oid
        ]);

        $sql = <<<sql
INSERT INTO usuarios_transacciones(id_usuario, id_moneda, costo_usuario_moneda, cantidad_usuario_moneda)
VALUES (:id_usuario, 'mxn', :costo_usuario_moneda, :cantidad_usuario_moneda);
sql;
        $mysql->prepare2($sql, [
            ':id_usuario' => $user_id,
            ':costo_usuario_moneda' => $trade->minor - $trade->fees_amount,
            ':cantidad_usuario_moneda' => $trade->minor - $trade->fees_amount
        ]);

        $this->setPrice();
    }

    function setPrice()
    {
        $sql = <<<sql
UPDATE usuarios_transacciones
SET
	precio_real_usuario_moneda = IF(id_moneda = 'mxn', 1, IF(cantidad_usuario_moneda!=0,costo_usuario_moneda / cantidad_usuario_moneda,0))
WHERE
	precio_real_usuario_moneda IS NULL;
sql;
        $mysql = new MySQL();
        $mysql->prepare2($sql);
    }

    public function selectTrades(int $id_usuario, string $id_moneda)
    {
        $this->setPrice();

        $sql = <<<sql
SELECT
    date,
    cost,
    prevmoneda,
    quantity,
    nextmoneda,
    price,
    round(accumulated_cost, 2) AS accumulated_cost,
    round(accumulated_quantity, 8) AS accumulated_quantity,
    round(accumulated_quantity, 8)*price AS accumulated_value,
    if(round(accumulated_cost, 2)<=0 or (coalesce(round(dca, 2),0)>price and percentage>0.90),price,coalesce(round(dca, 2),0)) AS dca,
    percentage,
    invalid
FROM (
         SELECT
             fecha_usuario_transaccion AS date,
             @last_price := precio_real_usuario_moneda AS price,
             costo_usuario_moneda AS cost,
             @running_cost := @running_cost + costo_usuario_moneda AS accumulated_cost,
             @running_quantity := @running_quantity + cantidad_usuario_moneda AS accumulated_quantity,
             @current_dca := @running_cost / @running_quantity AS dca,
             @prev_moneda := cantidad_usuario_moneda AS quantity,
             @prev_moneda := LAG(cantidad_usuario_moneda) OVER (ORDER BY fecha_usuario_transaccion) AS nextmoneda,
             @next_moneda := LEAD(cantidad_usuario_moneda) OVER (ORDER BY fecha_usuario_transaccion) AS prevmoneda,
             @percentage:=abs((precio_real_usuario_moneda-@current_dca)/@current_dca) as percentage,
             IF(cantidad_usuario_moneda+@prevmoneda=0 or cantidad_usuario_moneda+@nextmoneda=0, 1, 0) invalid
         FROM (
                  SELECT *
                  FROM usuarios_transacciones
                  WHERE id_usuario = :id_usuario
                    AND id_moneda = :id_moneda
                  ORDER BY fecha_usuario_transaccion
              ) t
                  JOIN (SELECT @running_cost := 0) rc
                  JOIN (SELECT @running_quantity := 0) rq
                  JOIN (SELECT @last_price := 0) p
                  JOIN (SELECT @invalid := 0) i
                  JOIN (SELECT @prev_moneda := NULL) pm
                  JOIN (SELECT @prev_dca := NULL) pd
                  JOIN (SELECT @current_dca := 0) cd
         ORDER BY fecha_usuario_transaccion
     ) AS subquery
WHERE subquery.invalid=0 and abs(cost)>1 and round(accumulated_quantity, 8)>0;
sql;
        $mysql = new MySQL();
        $query = $mysql->prepare2($sql, [
            ':id_usuario' => $id_usuario,
            ':id_moneda' => $id_moneda
        ]);
        return $query->fetchAll();
    }

    /**
     * @param int $id_usuario
     * @param string $id_moneda
     * @return array|false
     * @throws CoreException
     */
    function selectCalc(int $id_usuario, string $id_moneda): bool|array
    {
        $sql = <<<sql
SELECT *
FROM (
         SELECT
             id_moneda,
             fecha_usuario_transaccion AS fecha,
             @running_cost := ROUND(@running_cost + t.costo_usuario_moneda, 8) AS costo_actual,
             ROUND(@running_total, 8) AS cantidad_anterior,
             @total_anterior := ROUND(@running_total * @last_price, 2) AS total_anterior,
             @running_total := ROUND(@running_total + t.cantidad_usuario_moneda, 8) AS total_cantidad,
             @last_price := precio_real_usuario_moneda AS precio,
             @total_actual := ROUND(precio_real_usuario_moneda * @running_total, 2) AS total_actual,
             costo_usuario_moneda AS mxn,
             ROUND((costo_usuario_moneda) / @total_anterior, 2) AS porcentaje,
             @prev_moneda prevmoneda,
             @prev_moneda := cantidad_usuario_moneda AS moneda,
             @next_moneda := LEAD(cantidad_usuario_moneda) OVER (ORDER BY fecha_usuario_transaccion) AS nextmoneda
         FROM
             (SELECT * FROM usuarios_transacciones WHERE id_usuario = :id_usuario ORDER BY fecha_usuario_transaccion) t
                 JOIN (SELECT @running_cost := 0) c
                 JOIN (SELECT @running_total := 0) r
                 JOIN (SELECT @last_price := 0) p
                 JOIN (SELECT @invalid := 0) i
                 JOIN (SELECT @prev_moneda := NULL) pm
         WHERE id_moneda = :id_moneda
         ORDER BY fecha_usuario_transaccion
     ) AS subquery
         INNER JOIN monedas m ON subquery.id_moneda = m.id_moneda
where IF(moneda+prevmoneda=0 or moneda+nextmoneda=0, 1, 0)=0;
sql;
        $mysql = new MySQL();
        $query = $mysql->prepare2($sql, [
            ':id_usuario' => $id_usuario,
            ':id_moneda' => $id_moneda,
        ]);
        return $query->fetchAll();
    }
}
