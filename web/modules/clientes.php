<?php
?>
<div class="card">
    <div class="card-header">Actual</div>
    <div class="card-body padding">
        <div class="row">
            <div class="col-md-4">
                <label for="txtActualCartera" class="col-form-label font-weight-bold">Cartera</label>
                <input readonly="" class="form-control" type="text" id="txtActualCartera"/>
            </div>
            <div class="col-md-4">
                <label for="txtTotalClientes" class="col-form-label font-weight-bold">Clientes</label>
                <input readonly="" class="form-control" type="text" id="txtActualClientes"/>
            </div>
            <div class="col-md-4">
                <label for="txtTotalDiferencia" class="col-form-label font-weight-bold">Diferencia</label>
                <input readonly="" class="form-control" type="text" id="txtActualDiferencia"/>
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
<script src="assets/js/clientes.js"></script>
