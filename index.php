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
        getData("<?=$key?>", "<?=$nonce?>", "<?=$signature?>", "<?=$objective?>");
        setInterval(function () {
            location.reload();
        }, 3600000);
    });
</script>
Balance: <span id="bitcoin"></span><br>
MXN: (<span id="mxn"></span>)<br>
Localbitcoins: (<span id="localbitcoin"></span>)<br>
Objective: [<?= $objective ?>]<br>
Objective Bid: [<span id="objective"></span>]<br>
Total: <span id="total"></span>
<hr>
<br>

Localbitcoins<br>
Bid: <span id="bidlocalbitcoin"><?= $localbid ?></span><br>
Ask: <span id="localask"><?= $localask ?></span>

<hr>
<br>

Bitso<br>
<b>Bid: <span id="bid"></span></b><br>
Ask: <span id="ask"></span><br><!--
High: <span id="high"></span><br>
Last: <span id="last"></span><br>
Low: <span id="low"></span><br>
Vwap: <span id="vwap"></span><br>-->