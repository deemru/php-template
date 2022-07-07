<?php declare( strict_types = 1 );

namespace wavesplatform\Model;

class Block extends BlockHeaders
{
    /**
     * @return array<int, TransactionWithStatus>
     */
    function transactions(): array { return $this->get( 'transactions' )->asJson()->asArrayTransactionWithStatus(); }
    function fee(): int { return $this->get( 'fee' )->asInt(); }
}
