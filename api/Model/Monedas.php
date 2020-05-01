<?php


namespace Model;


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

    function selectMonedas()
    {
        $sql = <<<sql
SELECT * FROM monedas;
sql;
        $mysql = new MySQL();
        return $mysql->prepare2($sql)->fetchAll();
    }
}
