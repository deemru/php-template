<?php declare( strict_types = 1 );

namespace Waves\Transactions;

use deemru\WavesKit;
use Exception;
use Waves\Account\PrivateKey;
use Waves\Common\Base58String;
use Waves\Account\PublicKey;
use Waves\Common\Base64String;
use Waves\Common\ExceptionCode;
use Waves\Common\Json;
use Waves\Common\Value;
use Waves\Model\ChainId;
use Waves\Model\WavesConfig;

use Waves\Transactions\ReissueTransaction as CurrentTransaction;

class ReissueTransaction extends Transaction
{
    const TYPE = 5;
    const LATEST_VERSION = 3;
    const MIN_FEE = 100_000;

    private Amount $amount;
    private bool $isReissuable;

    static function build( PublicKey $sender, Amount $amount, bool $isReissuable ): CurrentTransaction
    {
        $tx = new CurrentTransaction;
        $tx->setBase( $sender, CurrentTransaction::TYPE, CurrentTransaction::LATEST_VERSION, CurrentTransaction::MIN_FEE );

        // REISSUE TRANSACTION
        {
            $tx->setAmount( $amount );
            $tx->setIsReissuable( $isReissuable );
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

        // REISSUE TRANSACTION
        {
            $pb_TransactionData = new \Waves\Protobuf\ReissueTransactionData;
            // AMOUNT
            {
                $pb_TransactionData->setAssetAmount( $this->amount()->toProtobuf() );
            }
            // REISSUABLE
            {
                $pb_TransactionData->setReissuable( $this->isReissuable() );
            }
        }

        // REISSUE TRANSACTION
        $this->setBodyBytes( $pb_Transaction->setReissue( $pb_TransactionData )->serializeToString() );
        return $this;
    }

    function amount(): Amount
    {
        if( !isset( $this->amount ) )
            $this->amount = Amount::fromJson( $this->json, 'quantity' );
        return $this->amount;
    }

    function setAmount( Amount $amount ): CurrentTransaction
    {
        $this->amount = $amount;
        $this->json->put( 'quantity', $amount->value() );
        $this->json->put( 'assetId', $amount->assetId()->toJsonValue() );
        return $this;
    }

    function isReissuable(): bool
    {
        if( !isset( $this->isReissuable ) )
            $this->isReissuable = $this->json->get( 'reissuable' )->asBoolean();
        return $this->isReissuable;
    }

    function setIsReissuable( bool $isReissuable ): CurrentTransaction
    {
        $this->isReissuable = $isReissuable;
        $this->json->put( 'reissuable', $isReissuable );
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
        return $this->setProofs( $proofs );
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
