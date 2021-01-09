<input type="hidden" id="user_token" value="<?= $_SESSION['user_token'] ?>">
<div class="card">
    <div class="card-header">Totales</div>
    <div class="card-body padding">
        <div class="row">
            <div class="col-md-4">
                <label class="col-form-label font-weight-bold">Costo</label>
                <input readonly="" class="form-control" type="text" id="txtTotalCosto"/>
            </div>
            <div class="col-md-4">
                <label class="col-form-label font-weight-bold">Actual</label>
                <input readonly="" class="form-control" type="text" id="txtTotalActual"/>
            </div>
            <div class="col-md-4">
                <label class="col-form-label font-weight-bold">Utilidad</label>
                <input readonly="" class="form-control" type="text" id="txtTotalGP"/>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body padding table-responsive">
        <table class="table"></table>
    </div>
</div>
<script>
    $(()=>{
        const cartera = new App.Cartera();
        cartera.initDatatable();
    });
</script>
