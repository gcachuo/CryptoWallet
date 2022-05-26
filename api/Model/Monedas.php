<?php


namespace Model;

use CoreException;
use Helper\Bitso;

class Monedas
{
    public function __construct()
    {
        $mysql = new MySQL();
        $mysql->create_table('monedas', [
            new TableColumn('id_moneda', ColumnTypes::VARCHAR, 5, true, null, false, true),
            new TableColumn('par_moneda', ColumnTypes::VARCHAR, 5, true),
            new TableColumn('nombre_moneda', ColumnTypes::VARCHAR, 100, true),
        ], <<<sql
create unique index monedas_nombre_moneda_uindex on monedas (nombre_moneda);
sql
            . $mysql->from_file('Monedas')
        );
    }

    /**
     * @param int $user_id
     * @return array
     * @throws CoreException
     */
    function selectMonedas(int $user_id): array
    {
        $sql = <<<sql
SELECT * FROM monedas;
sql;
        $mysql = new MySQL();
        $coins = $mysql->prepare2($sql)->fetchAll();

        $Bitso = new Bitso($user_id);
        $balances = $Bitso->selectBalances();

        $current_coins = [];
        foreach ($coins as $coin) {
            $current_coins[] = $coin['id_moneda'];
        }

        foreach ($balances as $balance) {
            $currency = $balance->currency;
            $index = in_array($currency, $current_coins);
            if ($index === false) {
                $moneda = $this->selectMoneda($currency);
                if (!$moneda && $currency !== 'mxn' && $balance->total > 0) {
                    $this->insertMoneda($currency, 'mxn', strtoupper($currency));
                }
            }
        }

        return $coins;
    }

    /**
     * @param string $id_moneda
     * @param string $par_moneda
     * @param string $nombre_moneda
     * @return int
     * @throws CoreException
     */
    private function insertMoneda(string $id_moneda, string $par_moneda, string $nombre_moneda): int
    {
        $sql = <<<sql
INSERT INTO monedas(id_moneda, par_moneda, nombre_moneda) VALUES(:id_moneda, :par_moneda, :nombre_moneda); 
sql;
        $mysql = new MySQL();
        $query = $mysql->prepare2($sql, [
            ':id_moneda' => $id_moneda,
            ':par_moneda' => $par_moneda,
            ':nombre_moneda' => $nombre_moneda,
        ]);
        return $query->lastInsertId();
    }

    private function selectMoneda(string $id_moneda)
    {
        $sql = <<<sql
SELECT * FROM monedas WHERE id_moneda=:id_moneda;
sql;
        $mysql = new MySQL();
        return $mysql->prepare2($sql, [':id_moneda' => $id_moneda])->fetch();
    }
}
