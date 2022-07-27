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
use wavesplatform\Model\AssetId;
use wavesplatform\Model\ChainId;
use wavesplatform\Model\WavesConfig;

use wavesplatform\Transactions\SponsorFeeTransaction as CurrentTransaction;

class SponsorFeeTransaction extends Transaction
{
    const TYPE = 14;
    const LATEST_VERSION = 2;
    const MIN_FEE = 100_000;

    private AssetId $assetId;
    private int $minSponsoredFee;

    static function build( PublicKey $sender, AssetId $assetId, int $minSponsoredFee ): CurrentTransaction
    {
        $tx = new CurrentTransaction;
        $tx->setBase( $sender, CurrentTransaction::TYPE, CurrentTransaction::LATEST_VERSION, CurrentTransaction::MIN_FEE );

        // SPONSORSHIP TRANSACTION
        {
            $tx->setAssetId( $assetId );
            $tx->setMinSponsoredFee( $minSponsoredFee );
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

        // SPONSORSHIP TRANSACTION
        {
            $pb_TransactionData = new \wavesplatform\Protobuf\SponsorFeeTransactionData;
            // ASSET + FEE
            {
                $pb_Amount = new \wavesplatform\Protobuf\Amount;
                $pb_Amount->setAmount( $this->minSponsoredFee() );
                if( !$this->assetId()->isWaves() )
                    $pb_Amount->setAssetId( $this->assetId()->bytes() );
                $pb_TransactionData->setMinFee( $pb_Amount );
            }
        }        

        // SPONSORSHIP TRANSACTION
        $this->setBodyBytes( $pb_Transaction->setSponsorFee( $pb_TransactionData )->serializeToString() );
        return $this;
    }

    function assetId(): AssetId
    {
        if( !isset( $this->assetId ) )
            $this->assetId = $this->json->get( 'assetId' )->asAssetId();
        return $this->assetId;
    }

    function setAssetId( AssetId $assetId ): CurrentTransaction
    {
        $this->assetId = $assetId;
        $this->json->put( 'assetId', $assetId->toJsonValue() );
        return $this;
    }

    function minSponsoredFee(): int
    {
        if( !isset( $this->minSponsoredFee ) )
            $this->minSponsoredFee = $this->json->get( 'minSponsoredAssetFee' )->asInt();
        return $this->minSponsoredFee;
    }

    function setMinSponsoredFee( int $minSponsoredFee ): CurrentTransaction
    {
        $this->minSponsoredFee = $minSponsoredFee;
        $this->json->put( 'minSponsoredAssetFee', $minSponsoredFee );
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