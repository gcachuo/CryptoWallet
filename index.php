<?php
/**
 * Created by PhpStorm.
 * User: Memo
 * Date: 11/ene/2017
 * Time: 03:58 PM
 */
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

$plusFee = 1 + ($balance->fee / 100);
$minusFee = 1 - ($balance->fee / 100);

$mxn = round($balance->btc_balance * ($ticker->ask * $plusFee), 2);
$local = round(($balance->btc_balance * $localbid), 2);
$btc = round($balance->mxn_balance / ($ticker->bid * $minusFee), 8);
$sellBtc = number_format(round(($btc - $objectiveBitcoin), 8), 8);
$sellMxn = $mxn - $objective;
$sellMxnFee = round(($sellBtc * ($ticker->bid * $minusFee)), 2);
?>
<script src="assets/plugins/jquery/jquery-3.1.1.min.js"></script>
<script src="assets/js/scripts.js"></script>
<script>/*
     $(function () {
     getData("<?=$key?>", "<?=$nonce?>", "<?=$signature?>", "<?=$objective?>", "<?=$objectiveBitcoin?>");
     setInterval(function () {
     location.reload();
     }, 3600000);
     });*/
</script>
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
<span id="sell"><?= $sellBtc . " (" . $sellMxnFee . " - " . ($ticker->bid * $minusFee) . ")" . "<br>" . $sellMxn . " (" . round(($sellMxn / ($ticker->ask * $plusFee)), 8) . " - " . ($ticker->ask * $plusFee) . ")" ?></span>
<br><br>

Bitso<br>
<table>
    <tbody>
    <tr>
        <td><input type="text" placeholder="BTC" onkeyup="changeBits($(this).val(),'btc')"></td>
        <td><span id="resAsk" title=""></span></td>
        <td>Ask: <span id="ask" title="<?= $ticker->ask ?>"><?= $ticker->ask * $plusFee ?></span></td>
        <td>High: <span id="high" title="<?= $ticker->high ?>"><?= $ticker->high * $plusFee ?></span></td>
    </tr>
    <tr>
        <td><input type="text" placeholder="MXN" onkeyup="changeBits($(this).val(),'mxn')"></td>
        <td><span id="resBid"></span></td>
        <td>Bid: <span id="bid" title="<?= $ticker->bid ?>"><?= $ticker->bid * $minusFee ?></span></td>
        <td>Low: <span id="low" title="<?= $ticker->low ?>"><?= $ticker->low * $minusFee ?></span></td>
    </tr>
    </tbody>
</table>
<br>
Last: <span id="last"><?= $ticker->last ?></span><br>
Vwap: <span id="vwap"><?= $ticker->vwap ?></span><br>
<hr>
<br>

Localbitcoins<br>
Bid: <span id="bidlocalbitcoin"><?= $localbid ?></span><br>
Ask: <span id="localask"><?= $localask ?></span>