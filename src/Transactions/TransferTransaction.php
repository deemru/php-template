<?php declare( strict_types = 1 );

namespace wavesplatform\Transactions;

use deemru\WavesKit;
use Exception;
use wavesplatform\Account\PrivateKey;
use wavesplatform\Common\Base58String;
use wavesplatform\Account\PublicKey;
use wavesplatform\Common\ExceptionCode;
use wavesplatform\Common\Json;
use wavesplatform\Model\ChainId;
use wavesplatform\Model\WavesConfig;

use wavesplatform\Transactions\TransferTransaction as CurrentTransaction;

class TransferTransaction extends Transaction
{
    const TYPE = 4;
    const LATEST_VERSION = 3;
    const MIN_FEE = 100_000;

    private Recipient $recipient;
    private Amount $amount;
    private Base58String $attachment;

    static function build( PublicKey $sender, Recipient $recipient, Amount $amount, Base58String $attachment = null ): CurrentTransaction
    {
        $tx = new CurrentTransaction;
        $tx->setBase( $sender, CurrentTransaction::TYPE, CurrentTransaction::LATEST_VERSION, CurrentTransaction::MIN_FEE );

        // TRANSFER TRANSACTION
        {
            $tx->setRecipient( $recipient );
            $tx->setAmount( $amount );
            $tx->setAttachment( $attachment ?? Base58String::emptyString() );
        }       

        return $tx;
    }

    function getUnsigned(): CurrentTransaction
    {
        // VERSION
        if( $this->version() !== CurrentTransaction::LATEST_VERSION )
            throw new Exception( __FUNCTION__ . ' unexpected version = ' . $this->version(), ExceptionCode::UNEXPECTED );

        // BASE
        $pb_Transaction = $this->getProtobufTransactionBase();

        // TRANSFER_TRANSACTION
        {
            $pb_TransactionData = new \wavesplatform\Protobuf\TransferTransactionData;
            // RECIPIENT
            {
                $pb_Recipient = new \wavesplatform\Protobuf\Recipient;
                if( $this->recipient()->isAlias() )
                    $pb_Recipient->setAlias( $this->recipient()->alias()->name() );
                else
                    $pb_Recipient->setPublicKeyHash( $this->recipient()->address()->publicKeyHash() );
                $pb_TransactionData->setRecipient( $pb_Recipient );
            }
            // AMOUNT
            {
                $pb_Amount = new \wavesplatform\Protobuf\Amount;
                $pb_Amount->setAmount( $this->amount()->value() );
                if( !$this->amount()->assetId()->isWaves() )
                    $pb_Amount->setAssetId( $this->amount()->assetId()->bytes() );
                $pb_TransactionData->setAmount( $pb_Amount );
            }
            // ATTACHMENT
            {
                $pb_TransactionData->setAttachment( $this->attachment()->bytes() );
            }
        }        

        $this->setBodyBytes( $pb_Transaction->setTransfer( $pb_TransactionData )->serializeToString() );
        return $this;
    }

    function recipient(): Recipient
    {
        if( !isset( $this->recipient ) )
            $this->recipient = $this->json->get( 'recipient' )->asRecipient();
        return $this->recipient;
    }

    function setRecipient( Recipient $recipient ): CurrentTransaction
    {
        $this->recipient = $recipient;
        $this->json->put( 'recipient', $recipient->toString() );
        return $this;
    }

    function amount(): Amount
    {
        if( !isset( $this->amount ) )
            $this->amount = Amount::of( $this->json->get( 'amount' )->asInt(), $this->json->get( 'assetId' )->asAssetId() );
        return $this->amount;
    }

    function setAmount( Amount $amount ): CurrentTransaction
    {
        $this->amount = $amount;
        $this->json->put( 'amount', $amount->value() );
        $this->json->put( 'assetId', $amount->assetId()->toJsonValue() );
        return $this;
    }

    function attachment(): Base58String
    {
        if( !isset( $this->attachment ) )
            $this->attachment = $this->json->get( 'attachment' )->asBase58String();
        return $this->attachment;
    }

    function setAttachment( Base58String $attachment ): CurrentTransaction
    {
        $this->attachment = $attachment;
        $this->json->put( 'attachment', $attachment->toString() );
        return $this;
    }
    
    // COMMON

    function __construct( Json $json = null )
    {
        parent::__construct( $json );
    }

    function addProof( PrivateKey $privateKey, int $index = null ): CurrentTransaction
    {
        $proof = (new WavesKit)->sign( $this->bodyBytes(), $privateKey->bytes() );
        if( $proof === false )
            throw new Exception( __FUNCTION__ . ' unexpected sign() error', ExceptionCode::UNEXPECTED );
        $proof = Base58String::fromBytes( $proof )->encoded();

        $proofs = $this->proofs();
        if( !isset( $index ) )
            $proofs[] = $proof;
        else
            $proofs[$index] = $proof;
        $this->setProofs( $proofs );
        return $this;
    }

    /**
     * @return CurrentTransaction
     */
    function setType( int $type )
    {
        parent::setType( $type );
        return $this;
    }

    /**
     * @return CurrentTransaction
     */
    function setSender( PublicKey $sender )
    {
        parent::setSender( $sender );
        return $this;
    }

    /**
     * @return CurrentTransaction
     */
    function setVersion( int $version )
    {
        parent::setVersion( $version );
        return $this;
    }

    /**
     * @return CurrentTransaction
     */
    function setFee( Amount $fee )
    {
        parent::setFee( $fee );
        return $this;
    }

    /**
     * @return CurrentTransaction
     */
    function setChainId( ChainId $chainId = null )
    {
        parent::setChainId( $chainId );
        return $this;
    }

    /**
     * @return CurrentTransaction
     */
    function setTimestamp( int $timestamp = null )
    {
        parent::setTimestamp( $timestamp );
        return $this;
    }

    /**
     * @param array<int, string> $proofs
     * @return CurrentTransaction
     */
    function setProofs( array $proofs = null )
    {
        parent::setProofs( $proofs );
        return $this;
    }

    function bodyBytes(): string
    {
        if( !isset( $this->bodyBytes ) )
            $this->getUnsigned();
        return parent::bodyBytes();
    }
}
