/**
 * Created by Memo on 11/ene/2017.
 */

$(function(){
    if($("#alertbits").html()*1>10 && $("#noalert").html()*1<10)
    alert($("#last").html());
});

function getData(key, nonce, signature, objective, objectiveBitcoin) {
    $.get("https://api.bitso.com/v2/ticker", function (ticker) {
        var minusFee = 1 - (1 / 100),
            plusFee = 1 + (1 / 100);
        $("#ask").attr("title", ticker.ask).html((ticker.ask * plusFee));
        $("#bid").attr("title", ticker.bid).html((ticker.bid * minusFee));
        $("#high").attr("title", ticker.high).html(ticker.high * plusFee);
        $("#low").attr("title", ticker.low).html(ticker.low * minusFee);
        $("#last").html(ticker.last);
        $("#vwap").html(ticker.vwap);
        $.post("https://api.bitso.com/v2/balance", {
            key: key,
            nonce: nonce,
            signature: signature
        }, function (balance) {
            var minusFee = 1 - (balance.fee / 100),
                plusFee = 1 + (balance.fee / 100);
            var btc = Math.round(balance.mxn_balance / (ticker.bid * minusFee) * 100000000) / 100000000;
            var mxn = Math.round(balance.btc_balance * (ticker.ask * plusFee) * 000000100) / 000000100;
            var objMxn = Math.round((objective / balance.btc_balance * plusFee) * 100) / 100;
            var objBtc = Math.round((balance.mxn_balance / objectiveBitcoin * plusFee) * 100) / 100;
            var sellBtc = Math.round((btc - objectiveBitcoin) * 100000000) / 100000000;
            var sellMxn = mxn - objective;
            var local = Math.round((balance.btc_balance * $("#bidlocalbitcoin").html()) * 100) / 100;
            $("#bitcoin").html(balance.btc_balance + " (" + balance.mxn_balance + ")");
            $("#btc").html(btc);
            $("#mxn").html(mxn);
            $("#objective").html(objMxn);
            $("#objectiveBitcoin").html(objBtc);
            var sellMxnFee = Math.round((sellBtc * (ticker.bid * minusFee)) * 100) / 100;
            $("#sell").html(sellBtc + " (" + sellMxnFee + " - " + (ticker.bid * minusFee) + ")" + "<br>" + sellMxn + " (" + Math.round((sellMxn / (ticker.ask * plusFee)) * 100000000) / 100000000 + " - " + (ticker.ask * plusFee) + ")");
            $("#localbitcoin").html(local);
            $("#total").html((Math.round((mxn + balance.mxn_balance * 1) * 100) / 100) + " | " + (local + balance.mxn_balance * 1));
            $.post("insertarHistorial.php", {
                mxn: (mxn + balance.mxn_balance * 1) + " | " + (local + balance.mxn_balance * 1),
                local: local,
                ask: ticker.ask * plusFee,
                bid: ticker.bid * minusFee
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