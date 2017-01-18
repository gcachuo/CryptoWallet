<?php
/**
 * Created by PhpStorm.
 * User: Memo
 * Date: 11/ene/2017
 * Time: 03:58 PM
 */
include "keys.php";
include "localbitcoins.php";
?>
<script src="assets/plugins/jquery/jquery-3.1.1.min.js"></script>
<script src="assets/js/scripts.js"></script>
<script>
    $(function () {
        getData("<?=$key?>", "<?=$nonce?>", "<?=$signature?>", "<?=$objective?>", "<?=$objectiveBitcoin?>");
        setInterval(function () {
            location.reload();
        }, 3600000);
    });
</script>
Balance: <span id="bitcoin"></span><br>
MXN: (<span id="mxn"></span>)<br>
Localbitcoins: (<span id="localbitcoin"></span>)<br>
Objective: [<?= $objective ?>][<?= $objectiveBitcoin ?>]<br>
Objective [Bid][Ask]: [<span id="objective"></span>][<span id="objectiveBitcoin"></span>]<br>
Total: <span id="total"></span>

<hr>
<br>

Bitso<br>
<input type="text" placeholder="BTC" onkeyup="changeBits($(this).val(),'btc')"><br>
<input type="text" placeholder="MXN" onkeyup="changeBits($(this).val(),'mxn')"><br>
<b>Bid: <span id="bid"></span></b> <span id="resBid"></span><br>
Ask: <span id="ask"></span> <span id="resAsk"></span><br><!--
High: <span id="high"></span><br>
Last: <span id="last"></span><br>
Low: <span id="low"></span><br>
Vwap: <span id="vwap"></span><br>-->
<hr>
<br>

Localbitcoins<br>
Bid: <span id="bidlocalbitcoin"><?= $localbid ?></span><br>
Ask: <span id="localask"><?= $localask ?></span>