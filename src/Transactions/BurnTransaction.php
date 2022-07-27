<?php declare( strict_types = 1 );

namespace wavesplatform\Transactions;

use deemru\WavesKit;
use Exception;
use wavesplatform\Account\PrivateKey;
use wavesplatform\Common\Base58String;
use wavesplatform\Account\PublicKey;
use wavesplatform\Common\Base64String;
use wavesplatform\Common\ExceptionCode;
use wavesplatform\Common\Json;
use wavesplatform\Common\Value;
use wavesplatform\Model\ChainId;
use wavesplatform\Model\WavesConfig;

use wavesplatform\Transactions\BurnTransaction as CurrentTransaction;

class BurnTransaction extends Transaction
{
    const TYPE = 6;
    const LATEST_VERSION = 3;
    const MIN_FEE = 100_000;

    private Amount $amount;

    static function build( PublicKey $sender, Amount $amount ): CurrentTransaction
    {
        $tx = new CurrentTransaction;
        $tx->setBase( $sender, CurrentTransaction::TYPE, CurrentTransaction::LATEST_VERSION, CurrentTransaction::MIN_FEE );

        // BURN TRANSACTION
        {
            $tx->setAmount( $amount );
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

        // BURN TRANSACTION
        {
            $pb_TransactionData = new \wavesplatform\Protobuf\BurnTransactionData;
            // AMOUNT
            {
                $pb_Amount = new \wavesplatform\Protobuf\Amount;
                $pb_Amount->setAmount( $this->amount()->value() );
                $pb_Amount->setAssetId( $this->amount()->assetId()->bytes() );
                $pb_TransactionData->setAssetAmount( $pb_Amount );
            }
        }        

        // BURN TRANSACTION
        $this->setBodyBytes( $pb_Transaction->setBurn( $pb_TransactionData )->serializeToString() );
        return $this;
    }

    function amount(): Amount
    {
        if( !isset( $this->amount ) )
            $this->amount = Amount::of( $this->json->get( 'quantity' )->asInt(), $this->json->get( 'assetId' )->asAssetId() );
        return $this->amount;
    }

    function setAmount( Amount $amount ): CurrentTransaction
    {
        $this->amount = $amount;
        $this->json->put( 'quantity', $amount->value() );
        $this->json->put( 'assetId', $amount->assetId()->toJsonValue() );
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
