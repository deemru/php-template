<?php declare( strict_types = 1 );

namespace wavesplatform\Transactions;

use wavesplatform\Common\Base58String;
use wavesplatform\Account\PublicKey;

class TransferTransaction extends Transaction
{
    const TYPE = 4;
    const LATEST_VERSION = 3;
    const MIN_FEE = 100_000;

    private PublicKey $sender;
    private Recipient $recipient;
    private Amount $amount;
    private Base58String $attachment;

    function __construct( PublicKey $sender, Recipient $recipient, Amount $amount, Base58String $attachment = null )
    {
        $this->sender = $sender;
        $this->recipient = $recipient;
        $this->amount = $amount;
        $this->attachment = $attachment ?? Base58String::emptyString();
    }

    function type(): int { return $this->get( 'type' )->asInt(); }
}
