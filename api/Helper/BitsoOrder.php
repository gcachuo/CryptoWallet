<?php

namespace Helper;

abstract class BitsoOrder
{
    public bool $success;
    /** @var BitsoOrderPayload $payload */
    public BitsoOrderPayload $payload;
}
