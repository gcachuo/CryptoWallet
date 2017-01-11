<?php
/**
 * Created by PhpStorm.
 * User: Memo
 * Date: 11/ene/2017
 * Time: 03:58 PM
 */
include "keys.php";
?>
<script src="assets/plugins/jquery/jquery-3.1.1.min.js"></script>
<script src="assets/js/scripts.js"></script>
<script>
    $(function () {
        getData("<?=$key?>", "<?=$nonce?>", "<?=$signature?>");
    });
</script>
Balance: <span id="bitcoin"></span><br>
MXN: <span id="mxn"></span>

<hr>
<br>

Ask: <span id="ask"></span><br>
<b>Bid: <span id="bid"></span></b><br>
High: <span id="high"></span><br>
Last: <span id="last"></span><br>
Low: <span id="low"></span><br>
Vwap: <span id="vwap"></span><br>