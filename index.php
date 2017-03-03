<?php
/**
 * Created by PhpStorm.
 * User: Memo
 * Date: 11/ene/2017
 * Time: 03:58 PM
 */

ini_set("display_errors", off);

include "keys.php";
include "localbitcoins.php";

$auth = array('Authorization: ' => $authHeader);

$ticker = request("https://api.bitso.com/v2/ticker/?book=btc_mxn");

generateSignature($key, $bitsoKey, $bitsoSecret, $nonce, $signature);
$keys = array("key" => $key, "nonce" => $nonce, "signature" => $signature);
$balance = request("https://api.bitso.com/v2/balance/", $keys);

generateSignature($key, $bitsoKey, $bitsoSecret, $nonce, $signature);
$keys = array("key" => $key, "nonce" => $nonce, "signature" => $signature);
$trades = request("https://api.bitso.com/v2/user_transactions/", $keys);
foreach ($trades as $trade) {
    $date = date('d/m/Y h:m:i', $trade->datetime);
    $htmlTrades .= <<<HTML
<tr>
<td>$date</td>
<td>$trade->btc</td>
<td>{round($trade->mxn,2)}</td>
<td>$trade->btc_mxn</td>
</tr>
HTML;

}

generateSignature($key, $bitsoKey, $bitsoSecret, $nonce, $signature);
$keys = array("key" => $key, "nonce" => $nonce, "signature" => $signature);
$orders = request("https://api.bitso.com/v2/open_orders?book=btc_mxn", $keys);
if ($orders[0]->type == "1") {
    $order = $orders[0]->amount . " - <span id='noalert'>" . round($orders[0]->amount * $orders[0]->price, 2) . "</span> - " . $orders[0]->price;
}

$plusFee = 1 + ($balance->fee / 100);
$minusFee = 1 - ($balance->fee / 100);

$mxn = round($balance->btc_balance * ($ticker->last * $plusFee), 2);
$local = round(($balance->btc_balance * $localbid), 2);
$btc = round($balance->mxn_balance / ($ticker->last * $minusFee), 8);
$sellBtc = number_format(round(($btc - $objectiveBitcoin), 8), 8);
$sellMxn = $mxn - $objective;
$sellMxnFee = round(($sellBtc * ($ticker->last * $minusFee)), 2);
?>
<head>
    <!-- x/60=t 900/60=15min -->
    <meta http-equiv="refresh" content="900">
    <link rel="icon" type="image/png" href="assets/img/icon.png"/>
    <link rel="manifest" href="assets/manifest.json">
    <script src="assets/plugins/jquery/jquery-3.1.1.min.js"></script>
    <script src="assets/js/scripts.js"></script>
</head>

Balance: <span id="bitcoin"><?= $balance->btc_balance ?> (<?= $balance->mxn_balance ?>)</span><br>
Bitcoin: <span id="btc"><?= $btc ?></span><br>
MXN: (<span id="mxn"><?= $mxn ?></span>)<br>
Localbitcoins: (<span id="localbitcoin"><?= $local ?></span>)<br>
Objective: [<?= $objective ?>][<?= $objectiveBitcoin ?>]<br>
Objective [Bid][Ask]: [<span id="objective"><?= round(($objective / $balance->btc_balance * $plusFee), 2) ?></span>]
[<span id="objectiveBitcoin"><?= round(($balance->mxn_balance / $objectiveBitcoin * $plusFee), 2) ?></span>]<br>
Total: <span id="total"><?= round($mxn + $balance->mxn_balance, 2) . " | " . ($local + $balance->mxn_balance) ?></span>

<hr>
<br>

Sell<br>
<span id="sell"><?= $sellBtc . " (" . $sellMxnFee . " - " . ($ticker->last * $minusFee) . ")" . "<br><span id='alertbits'>" . round($sellMxn, 2) . "</span> (" . number_format(round(($sellMxn / ($ticker->last * $plusFee)), 8), 8) . " - " . round($ticker->last * $plusFee, 2) . ")" ?></span>
<br><br>
Last: <span id="last"><?= $ticker->last ?> (<?= $ticker->last * $plusFee ?>) (<?= $ticker->last * $minusFee ?>)</span>
<br><br>

Orders<br>
<div id="orders">
    <?= $order ?>
</div><br>

Bitso<br>
<table>
    <tbody>
    <tr>
        <td><input type="text" placeholder="BTC" onkeyup="changeBits($(this).val(),'btc')"></td>
        <td><span id="resAsk" title=""></span></td>
        <td>Ask: <span id="ask" title="<?= $ticker->ask ?>"><?= round($ticker->ask * $plusFee, 2) ?></span></td>
        <td>High: <span id="high" title="<?= $ticker->high ?>"><?= round($ticker->high * $plusFee, 2) ?></span></td>
    </tr>
    <tr>
        <td><input type="text" placeholder="MXN" onkeyup="changeBits($(this).val(),'mxn')"></td>
        <td><span id="resBid"></span></td>
        <td>Bid: <span id="bid" title="<?= $ticker->bid ?>"><?= $ticker->bid * $minusFee ?></span></td>
        <td>Low: <span id="low" title="<?= $ticker->low ?>"><?= round($ticker->low * $minusFee, 2) ?></span></td>
    </tr>
    </tbody>
</table>
<br>
Vwap: <span id="vwap"><?= $ticker->vwap ?></span><br>
<hr>
<br>
<div id="trades">
    <table>
        <tbody>
        <?= $htmlTrades ?>
        </tbody>
    </table>
</div>
<hr>
<br>
Localbitcoins<br>
Bid: <span id="bidlocalbitcoin"><?= $localbid ?></span><br>
Ask: <span id="localask"><?= $localask ?></span>