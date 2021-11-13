<?php

namespace Helper;

abstract class BitsoOrderPayload
{
    public $original_value;
    public $unfilled_amount;
    public $original_amount;
    public $book;
    public $created_at;
    public $updated_at;
    public $side;
    public $type;
    public $oid;
    public $status;
    public $price;
    public $time_in_force;
}
