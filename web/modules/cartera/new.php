<?php

use Model\Monedas;

System::init(['DIR' => __DIR__ . '/../../../api']);

$Monedas = new Monedas();
$monedas = $Monedas->selectMonedas();
?>
<form class="container" method="post" action="cartera/ajax/save.php">
    <div class="form-group">
        <select name="id_moneda" class="form-control">
            <?php foreach ($monedas as $moneda): ?>
                <option <?= $moneda['id_moneda'] == 'btc' ? 'selected' : '' ?>
                        value="<?= $moneda['id_moneda'] ?>"><?= $moneda['nombre_moneda'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <input type="number" placeholder="Costo (MXN)" class="form-control" min="0" step="0.01" required>
    </div>
    <div class="form-group">
        <input type="number" placeholder="Cantidad" class="form-control" min="0" step="0.00000001" required>
    </div>
    <button class="btn btn-primary">Guardar</button>
</form>
<script src="assets/js/cartera/new.js"></script>
