<?php declare( strict_types = 1 );

namespace wavesplatform\Model;

class Transaction extends TransactionOrOrder
{
    function type(): int { return $this->get( 'type' )->asInt(); }
}
