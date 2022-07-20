<?php
System::check_value_empty($_GET, ['coin']);
?>
<div class='card'>
    <div class='card-header'>
        <h1>Compras: <span id='buys' class='text-success'>$0</span></h1>
        <h1>Ventas: <span id='sells' class='text-danger'>$0</span></h1>
        <h1>Actual: <span id='total' class='text-danger'>$0</span></h1>
        <h1>Utilidad: <span id='profits' class='text-info'>$0</span></h1>
    </div>
</div>
<div class='card'>
    <div class='card-header'>Utilidad | <?= strtoupper($_GET['coin'] ?? null) ?></div>
    <div class="card-body padding">
        <table></table>
    </div>
</div>
<script>
    $(()=>{
        const utilidad = new App.Utilidad();
        utilidad.initDatatable('<?= $_GET['coin'] ?>');
    });
</script>
