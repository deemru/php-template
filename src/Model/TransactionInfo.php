<?php declare( strict_types = 1 );

namespace wavesplatform\Model;

class TransactionInfo extends TransactionWithStatus
{
    function height(): int { return $this->json->get( 'height' )->asInt(); }
}
