<?php

namespace Helper;

abstract class BitsoTradePayload
{
    public string $book;
    public string $created_at;
    public float $minor;
    public float $major;
    public float $fees_amount;
    public float $fees_currency;
    public float $minor_currency;
    public float $major_currency;
    public float $oid;
    public float $tid;
    public float $price;
    public float $side;
    public float $maker_side;
}
