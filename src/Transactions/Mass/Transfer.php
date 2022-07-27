<?php declare( strict_types = 1 );

namespace wavesplatform\Transactions\Mass;

use wavesplatform\Transactions\Recipient;

class Transfer
{
    private Recipient $recipient;
    private int $amount;

    function __construct( Recipient $recipient, int $amount )
    {
        $this->recipient = $recipient;
        $this->amount = $amount;
    }

    function recipient(): Recipient
    {
        return $this->recipient;
    }

    function amount(): int
    {
        return $this->amount;
    }
}
