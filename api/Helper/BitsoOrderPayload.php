<?php

namespace Helper;

abstract class BitsoOrderPayload
{
    public float $unfilled_amount;
    public string $book;
    public string $created_at;
    public string $updated_at;
    public string $side;
    public string $type;
    public string $oid;
    public string $status;
    public float $original_value;
}
