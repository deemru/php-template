<?php declare( strict_types = 1 );

namespace wavesplatform\Transactions;

use deemru\WavesKit;
use wavesplatform\Common\Base58String;
use wavesplatform\Account\PublicKey;
use wavesplatform\Common\Json;

class TransferTransaction extends Transaction
{
    const TYPE = 4;
    const VERSION = 3;
    const MIN_FEE = 100_000;

    private PublicKey $sender;
    private Recipient $recipient;
    private Amount $amount;
    private Base58String $attachment;

    function __construct( PublicKey $sender, Recipient $recipient, Amount $amount, Base58String $attachment = null )
    {
        parent::__construct();
        $this->sender = $sender;
        $this->recipient = $recipient;
        $this->amount = $amount;
        $this->attachment = $attachment ?? Base58String::emptyString();
        (new WavesKit())->txTransfer();

        $tx =
        [
            'type' => TransferTransaction::TYPE,
            'version' => TransferTransaction::VERSION,
            'sender' => $sender->address()->toString(),
            'senderPublicKey' => $sender->toString(),
            'recipient' => $recipient->toString(),
            'amount' => $amount->value(),
            'assetId' => $amount->toString(),
            'amount' => $fee->value(),
            'assetId' => $fee->toString(),
        ];
        $tx['timestamp'] = isset( $options['timestamp'] ) ? $options['timestamp'] : $this->timestamp();
        if( isset( $options['attachment'] ) ) $tx['attachment'] = $options['attachment'];
        return $tx;
    }
}
