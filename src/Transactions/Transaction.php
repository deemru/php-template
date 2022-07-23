<?php declare( strict_types = 1 );

namespace wavesplatform\Transactions;

class Transaction extends TransactionOrOrder
{
    function type(): int { return $this->json->get( 'type' )->asInt(); }
}
