/**
 * Created by Memo on 11/ene/2017.
 */

function getData(key, nonce, signature, objective, objectiveBitcoin) {
    $.get("https://api.bitso.com/v2/ticker", function (ticker) {
        $("#ask").html((ticker.ask * 0.99));
        $("#bid").html((ticker.bid * 1.01));
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
            $("#objective").html((Math.round((objective / balance.btc_balance * 1.01) * 100) / 100));
            $("#objectiveBitcoin").html((Math.round((balance.mxn_balance / objectiveBitcoin * 1.01) * 100) / 100));
            $("#localbitcoin").html(local);
            $("#total").html((Math.round((mxn * 1 + balance.mxn_balance * 1) * 100) / 100) + " | " + (local * 1 + balance.mxn_balance * 1));
            $.post("insertarHistorial.php", {
                mxn: (mxn * 1 + balance.mxn_balance * 1) + " | " + (local * 1 + balance.mxn_balance * 1),
                local: local,
                ask: ticker.ask * 0.99,
                bid: ticker.bid * 1.01
            });
        }, 'json');
    }, 'json');
}

function changeBits(val, currency) {
    switch (currency) {
        case 'btc':
            var resBid = Math.round(val * $("#bid").html() * 100) / 100;
            var resAsk = Math.round(val * $("#ask").html() * 100) / 100;
            $("#resBid").html("Buy: " + resBid);
            $("#resAsk").html("Sell: " + resAsk);
            break;
        case 'mxn':
            var resBid = Math.round(val / $("#bid").html() * 100000000) / 100000000;
            var resAsk = Math.round(val / $("#ask").html() * 100000000) / 100000000;
            $("#resBid").html("Sell: " + resBid);
            $("#resAsk").html("Buy: " + resAsk);
            break;
    }
}