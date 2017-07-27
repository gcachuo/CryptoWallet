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
$objectiveBitcoin = 0;
foreach ($trades as $trade) {
    $firstDate = date_create_from_format('Y-m-d', "2017-02-01");
    $date = date_create_from_format('Y-m-d H:i:s', $trade->datetime);

    $btc_mxn = $trade->btc < 0 ? $trade->btc_mxn * 0.99 : $trade->btc_mxn * 1.01;
    if ($btc_mxn == 0) continue;

    if ($date > $firstDate and $trade->btc < 0) {
        switch ($trade->id) {
            case 300933:
                continue;
            default:
                $objectiveBitcoin += $trade->btc;
                break;
        }
    } /*elseif ($trade->mxn < 0 and $trade->btc_mxn == 0)
        $objectiveBitcoin += -($trade->mxn / 23487.5803);*/

    $printdate = $date->format('d/m/Y');
    $roundmxn = round($trade->mxn, 2);
    $tradeBtc = number_format(round($trade->btc, 8), 8);
    $htmlTrades .= <<<HTML
<tr>
    <td>$printdate</td>
    <td>$tradeBtc</td>
    <td>$roundmxn</td>
    <td>$btc_mxn</td>
</tr>
HTML;
}
$objectiveBitcoin = abs($objectiveBitcoin);/*
$objectiveBitcoin -= 0.01096820 /*0.01110164 /*0.01085961
;*/
$objectiveBitcoin = number_format(round($objectiveBitcoin * 1.02, 8), 8);

generateSignature($key, $bitsoKey, $bitsoSecret, $nonce, $signature);
$keys = array("key" => $key, "nonce" => $nonce, "signature" => $signature);
$orders = request("https://api.bitso.com/v2/open_orders?book=btc_mxn", $keys);
if ($orders[0]->type == "1") {
    generateSignature($key, $bitsoKey, $bitsoSecret, $nonce, $signature);
    $order = $orders[0]->amount * -1 . " <span id='noalert'>" . round($orders[0]->amount * $orders[0]->price, 2) . "</span> " . $orders[0]->price . "<button onclick='cancel(\"{$orders[0]->id}\", \"$key\", \"$nonce\", \"$signature\")'>Cancel</button>";
} elseif ($orders[0]->type == "0") {
    generateSignature($key, $bitsoKey, $bitsoSecret, $nonce, $signature);
    $order = $orders[0]->amount . " | -" . round($orders[0]->amount * $orders[0]->price, 2) . " | " . $orders[0]->price . "<button onclick='cancel(\"{$orders[0]->id}\", \"$key\", \"$nonce\", \"$signature\")'>Cancel</button>";
}

$plusFee = 1 + ($balance->fee / 100);
$minusFee = 1 - ($balance->fee / 100);
$plusWithdraw = 0;

$mxn = round($balance->btc_balance * ($ticker->last * $plusFee), 2);
$local = round(($balance->btc_balance * $localbid), 2);
$btc = round(($balance->mxn_balance + $plusWithdraw) / ($ticker->last * $minusFee), 8);
$sellBtc = number_format(round(($btc - $objectiveBitcoinFix), 8), 8);
$sellMxn = $mxn - $objective;
$sellMxnFee = round(($sellBtc * ($ticker->last * $minusFee)), 2);
?>
<head>
    <!-- x/60=t 900/60=15min -->
    <meta http-equiv="refresh" content="900">
    <link rel="icon" type="image/png" href="assets/img/icon.png"/>
    <link rel="manifest" href="assets/manifest.json">
    <title>Bitcoin Wallet</title>
    <script src="assets/plugins/jquery/jquery-3.1.1.min.js"></script>
    <script src="assets/js/scripts.js"></script>
</head>
<style>
    table, th, td {
        border: solid 1px black;
        text-align: center;
    }
</style>
<table>
<tbody>
<tr>
<td>Balance</td>
<td><span id="bitcoin"><?= $balance->btc_balance ?> (<?= $balance->mxn_balance ?>)</span></td>
</tr>
<tr>
<td>Bitcoin</td>
<td><span id="btc"><?= $btc ?></span></td>
</tr>
<tr>
<td>MXN</td>
<td>(<span id="mxn"><?= $mxn ?></span>)</td>
</tr>
<tr>
<td>Localbitcoins</td>
<td>(<span id="localbitcoin"><?= $local ?></span>)</td>
</tr>
<tr>
<td>Objective</td>
<td>[<?= $objective ?>][<?= $objectiveBitcoinFix ?>]</td>
</tr>
<tr>
<td>Objective [Bid][Ask]</td>
<td>[<span id="objective"><?= round(($objective / $balance->btc_balance * $plusFee), 2) ?></span>]
[<span id="objectiveBitcoin"><?= round((($balance->mxn_balance + $plusWithdraw) / ($objectiveBitcoinFix * $plusFee)), 2) ?></span>]</td>
</tr>
<tr>
<td>Total</td>
<td><span id="total"><?= round($mxn + $balance->mxn_balance, 2) . " | " . ($local + $balance->mxn_balance) ?></span></td>
</tr>
</tbody>
</table>
: <br>
: <br>
: <br>
: <br>
: 
<br>
: 

<hr>
<br>

Sell<br>
<span id="sell">
    <?= $sellBtc . " (" . $sellMxnFee . " - " . round($ticker->last * $minusFee, 2) . ")" ?>
    <?php generateSignature($key, $bitsoKey, $bitsoSecret, $nonce, $signature);
    if ($sellMxnFee > 0):
        ?>
        <button onclick="buy(<?= $sellBtc ?>,<?= round($ticker->last * $minusFee, 2) ?>,'<?= $key ?>','<?= $nonce ?>','<?= $signature ?>')">Buy</button>
    <?php endif; ?>
    <br>
    <span id='alertbits'><?= round($sellMxn, 2) ?></span>
    (<?= number_format(round(($sellMxn / ($ticker->last * $plusFee)), 8), 8) . " - " . round($ticker->last * $plusFee, 2) . ")" ?>
</span>
<br><br>
Last: <span id="last"><?= $ticker->last ?> (<?= round($ticker->last * $plusFee, 2) ?>) (<?= $ticker->last * $minusFee ?>
    )</span>
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
<hr>
<br>
<?php
include "address.php";
?>