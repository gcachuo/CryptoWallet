$(function () {
    $("body > main > header").css('display', 'flex');
    cantidades();
    Project.refreshInterval = setInterval(cantidades, 60000);
});

async function cantidades() {
    const $loading = $(".loading");
    const $table = $("#tableCoins");
    $loading.show();
    const totales = {
        costo: 0,
        actual: 0
    };
    const amounts = await Project.Users.fetchAmounts();
    $("#tableCoins").find('tbody').html('');
    amounts.sort(function (a, b) {
        return b['porcentaje'] - a['porcentaje'];
    });
    $.each(amounts, function (key, coin) {
        totales.costo += coin.costo * 1;
        totales.actual += coin.total;

        coin.costo = numeral(coin.costo).format('$0,0.00');
        coin.precio = numeral(coin.precio).format('$0,0.00');
        coin.promedio = numeral(coin.promedio).format('$0,0.00');
        coin.total = numeral(coin.total).format('$0,0.00');
        coin.porcentaje = numeral(coin.porcentaje).format('0,0.00%');
        coin.cantidad = numeral(coin.cantidad).format('0.00000000');
        $table.find('tbody').append(`
<tr>
    <td>${coin.moneda}</td>
    <td>${coin.cantidad}</td>
    <td>${coin.precio}</td>
    <td>${coin.promedio}</td>
    <td>${coin.costo}</td>
    <td>${coin.total}</td>
    <td>${coin.porcentaje}</td>
</tr>
               `);
    });
    $("#txtTotalCosto").val(numeral(totales.costo).format('$0,0.00'));
    $("#txtTotalActual").val(numeral(totales.actual).format('$0,0.00'));
    $("#txtTotalGP").val(numeral(totales.actual - totales.costo).format('$0,0.00'));

    console.log('finish: ' + Date().toString());
    $loading.hide();
    $table.dataTable({
        destroy: true,
        responsive: true,
        paginate: false,
        searching: false,
        order: false
    });
}