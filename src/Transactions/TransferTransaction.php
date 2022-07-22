<?php declare( strict_types = 1 );

namespace wavesplatform\Transactions;

use wavesplatform\Common\Base58String;

class TransferTransaction extends Transaction
{
    const TYPE = 4;
    const LATEST_VERSION = 3;
    const MIN_FEE = 100_000;

    private Recipient $recipient;
    private Amount $amount;
    private Base58String $attachment;

    function __construct()
    {
        
    }

    function type(): int { return $this->get( 'type' )->asInt(); }
}
