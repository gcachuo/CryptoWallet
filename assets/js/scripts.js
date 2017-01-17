/**
 * Created by Memo on 11/ene/2017.
 */

function getData(key, nonce, signature, objective) {
    $.get("https://api.bitso.com/v2/ticker", function (ticker) {
        $("#ask").html((ticker.ask * 0.99));
        $("#bid").html((ticker.bid * 0.99));
        $("#high").html(ticker.high);
        $("#last").html(ticker.last);
        $("#low").html(ticker.low);
        $("#vwap").html(ticker.vwap);
        $.post("https://api.bitso.com/v2/balance", {
            key: key,
            nonce: nonce,
            signature: signature
        }, function (balance) {
            console.log(balance);
            var mxn = Math.round((balance.btc_balance * (ticker.ask * 0.99)) * 100) / 100;
            var local = Math.round((balance.btc_balance * $("#bidlocalbitcoin").html()) * 100) / 100;
            $("#bitcoin").html(balance.btc_balance + " (" + balance.mxn_balance + ")");
            $("#mxn").html(mxn);
            $("#objective").html((Math.round((objective / balance.btc_balance) * 100) / 100) * 1.01);
            $("#localbitcoin").html(local);
            $("#total").html((Math.round((mxn * 1 + balance.mxn_balance * 1)*100)/100) + " | " + (local * 1 + balance.mxn_balance * 1));
            $.post("insertarHistorial.php", {cash: (mxn * 1 + balance.mxn_balance * 1) + " | " + (local * 1 + balance.mxn_balance * 1)});
        }, 'json');
    }, 'json');
}