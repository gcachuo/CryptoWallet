/**
 * Created by Memo on 11/ene/2017.
 */

function getData(key, nonce, signature, objective) {
    $.get("https://api.bitso.com/v2/ticker", function (get) {
        $("#ask").html(get.ask);
        $("#bid").html(get.bid + " (" + (get.bid - (get.bid * 0.01)) + ")");
        $("#high").html(get.high);
        $("#last").html(get.last);
        $("#low").html(get.low);
        $("#vwap").html(get.vwap);
        $.post("https://api.bitso.com/v2/balance", {
            key: key,
            nonce: nonce,
            signature: signature
        }, function (post) {
            var mxn = Math.round((post.btc_available * (get.bid - (get.bid * 0.01))) * 100) / 100;
            $("#bitcoin").html(post.btc_available);
            $("#mxn").html(mxn);
            $("#objective").html(Math.round((objective / post.btc_available) * 100) / 100);
            $.post("insertarHistorial.php", {cash: mxn});
        }, 'json');
    }, 'json');
}