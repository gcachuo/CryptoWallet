<?php

namespace Helper;

class BitsoTradePayload extends \stdClass
{
    public string $book;
    public string $created_at;
    public string $minor;
    public string $major;
    public string $fees_amount;
    public string $fees_currency;
    public string $minor_currency;
    public string $major_currency;
    public string $oid;
    public string $tid;
    public string $price;
    public string $side;
    public string $maker_side;

    public function __construct(array $trade = [])
    {
        if (!empty($trade)) {
            $this->book = $trade['book'];
            $this->created_at = $trade['created_at'];
            $this->minor = $trade['minor'];
            $this->major = $trade['major'];
            $this->fees_amount = $trade['fees_amount'];
            $this->fees_currency = $trade['fees_currency'];
            $this->minor_currency = $trade['minor_currency'];
            $this->major_currency = $trade['major_currency'];
            $this->oid = $trade['oid'];
            $this->tid = $trade['tid'];
            $this->price = $trade['price'];
            $this->side = $trade['side'];
            $this->maker_side = $trade['maker_side'];
        }
    }
}
