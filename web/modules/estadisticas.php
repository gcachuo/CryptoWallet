<?php
try {
    System::check_value_empty($_GET, ['coin']);
}catch (CoreException $exception){
    System::redirect('./');
}
?>
<div class="card">
    <div class="card-header">Estadisticas | <?= strtoupper($_GET['coin'] ?? null) ?></div>
    <div class="card-body padding" style="height: calc(100vh - 166px)">
        <div id="chartdiv" style="width: 100%; height: 100%"></div>
    </div>
</div>
<input type="hidden" id="coin" value="<?= $_GET['coin'] ?? null ?>">
<script>
    $(() => {
        const estadisticas = new App.Estadisticas();
        estadisticas.getTradesChart($("#coin").val());
    });
</script>
