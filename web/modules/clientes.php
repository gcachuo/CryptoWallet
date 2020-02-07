<?php
?>
<div class="card">
    <div class="card-header">Totales</div>
    <div class="card-body padding">
        <div class="row">
            <div class="col-md-4">
                <label for="txtTotalCartera" class="col-form-label font-weight-bold">Cartera</label>
                <input readonly="" class="form-control" type="text" id="txtTotalCartera"/>
            </div>
            <div class="col-md-4">
                <label for="txtTotalClientes" class="col-form-label font-weight-bold">Clientes</label>
                <input readonly="" class="form-control" type="text" id="txtTotalClientes"/>
            </div>
            <div class="col-md-4">
                <label for="txtTotalDiferencia" class="col-form-label font-weight-bold">Diferencia</label>
                <input readonly="" class="form-control" type="text" id="txtTotalDiferencia"/>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-header">Monedas</div>
    <div class="card-body padding table-responsive">
        <table id="tabla-monedas" class="table"></table>
    </div>
</div>
<div class="card">
    <div class="card-header">Clientes</div>
    <div class="card-body padding table-responsive">
        <table id="tabla-clientes" class="table"></table>
    </div>
</div>
<script src="assets/clientes.js"></script>
