<?php
$monedas = System::curl(['url' => 'coins/list'])['monedas'];
?>
<div class="padding">
    <form class="container" method="put" uri="users/trade" callback="addTrade">
        <input type="hidden" name="id_usuario">
        <div class="form-group">
            <select name="tipo" class="form-control" required>
                <option value="">Seleccione Tipo</option>
                <option value="ingreso">Ingreso</option>
                <option value="retiro">Retiro</option>
            </select>
        </div>
        <div class="form-group">
            <select name="id_moneda" class="form-control" required>
                <option value="">Seleccione Moneda</option>
                <?php foreach ($monedas as $moneda): ?>
                    <option value="<?= $moneda['id_moneda'] ?>"><?= $moneda['nombre_moneda'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <input type="number" name="costo" placeholder="Costo (MXN)" class="form-control" min="0" step="0.01"
                   required>
        </div>
        <div class="form-group">
            <input type="number" name="cantidad" placeholder="Cantidad" class="form-control" min="0" step="0.00000001"
                   required>
        </div>
        <button class="btn btn-primary">Guardar</button>
    </form>
    <script src="assets/js/cartera/new.js"></script>
</div>
