<?php
/**
 * Created by PhpStorm.
 * User: Memo
 * Date: 12/ene/2017
 * Time: 04:07 PM
 */

$fh = fopen('historial.txt', 'r');
$date = date('d-m-Y');
while ($line = fgets($fh)) {
    $items .= <<<XML
<item>
    <title>$line</title>
    <link>$line</link>
    <pubDate>$date</pubDate>
    <category>Money</category>
    <description>$line</description>
</item>
XML;
}
fclose($fh);

header('Content-type: text/xml; charset="iso-8859-1"', true);

echo <<<XML
<?xml version="1.0" encoding="iso-8859-1"?>
<rss version="2.0">
    <channel>
        <title>Bitcoin Balance</title>
        <link>http://gcachuo.pe.hu/BitcoinWallet/historial.txt</link>
        <language>es-MX</language>
        <description>Bitcoin Balance in Bitso (MXN)</description>
        <generator>Guillermo Cachu Osorio</generator>
        $items
    </channel>
</rss>
XML;
