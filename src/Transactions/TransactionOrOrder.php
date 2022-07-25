<?php declare( strict_types = 1 );

namespace wavesplatform\Transactions;

use deemru\WavesKit;
use wavesplatform\Common\Json;
use wavesplatform\Common\JsonBase;
use wavesplatform\Account\PublicKey;
use wavesplatform\Model\AssetId;
use wavesplatform\Model\ChainId;
use wavesplatform\Model\Id;
use wavesplatform\Model\WavesConfig;
use wavesplatform\Util\Functions;

class TransactionOrOrder extends JsonBase
{
    private Id $id;
    private int $version;
    private ChainId $chainId;
    private PublicKey $sender;
    private int $timestamp;
    private Amount $fee;
    /**
     * @var array<int, string>
     */
    private array $proofs;
    private string $bodyBytes;

    function id(): Id
    {
        if( !isset( $this->id ) )
        {
            if( !$this->json()->exists( 'id' ) && isset( $this->bodyBytes ) )
                $this->setId( Functions::calculateTransactionId( $this->bodyBytes ) );
            else
                $this->id = $this->json->get( 'id' )->asId();
        }
        return $this->id;
    }

    function setId( Id $id ): void
    {
        $this->id = $id;
        $this->json->put( 'id', $id->toString() );
    }
    
    function version(): int
    {
        if( !isset( $this->version ) )
            $this->version = $this->json->get( 'version' )->asInt();
        return $this->version;
    }

    function setVersion( int $version ): void
    {
        $this->version = $version;
        $this->json->put( 'version', $version );
    }

    function chainId(): ChainId
    {
        if( !isset( $this->chainId ) )
        {
            if( $this->json->exists( 'chainId' ) )
                $this->chainId = $this->json->get( 'chainId' )->asChainId();
            else if( $this->json->exists( 'sender' ) )
                $this->chainId = $this->json->get( 'sender' )->asAddress()->chainId();
            else
                $this->chainId = WavesConfig::chainId();
        }
        return $this->chainId;
    }

    function setChainId( ChainId $chainId = null ): void
    {
        if( !isset( $chainId ) )
            $chainId = WavesConfig::chainId();
        $this->chainId = $chainId;
        $this->json->put( 'chainId', $chainId->asInt() );
    }
    
    function sender(): PublicKey
    {
        if( !isset( $this->sender ) )
        {
            $this->sender = $this->json->get( 'senderPublicKey' )->asPublicKey();
            if( $this->json->exists( 'sender' ) )
                $this->sender->attachAddress( $this->json->get( 'sender' )->asAddress() );
        }
        return $this->sender;
    }

    function setSender( PublicKey $sender ): void
    {
        $this->sender = $sender;
        $this->json->put( 'senderPublicKey', $sender->toString() );
        $this->json->put( 'sender', $sender->address()->toString() );
    }

    function timestamp(): int
    {
        if( !isset( $this->timestamp ) )
            $this->timestamp = $this->json->get( 'timestamp' )->asInt();
        return $this->timestamp;
    }

    function setTimestamp( int $timestamp = null ): void
    {
        if( !isset( $timestamp ) )
            $timestamp = intval( microtime( true ) * 1000 );
        $this->timestamp = $timestamp;
        $this->json->put( 'timestamp', $timestamp );
    }

    function fee(): Amount
    {
        if( !isset( $this->fee ) )
            $this->fee = Amount::of( $this->json->get( 'fee' )->asInt(), $this->json->getOr( 'feeAssetId', AssetId::WAVES_STRING )->asAssetId() );
        return $this->fee;
    }

    function setFee( Amount $fee ): void
    {
        $this->fee = $fee;
        $this->json->put( 'fee', $fee->value() );
        $this->json->put( 'feeAssetId', $fee->assetId()->toJsonValue() );
    }

    /**
     * @return array<int, string>
     */
    function proofs(): array 
    {
        if( !isset( $this->proofs ) )
            $this->proofs = $this->json->get( 'proofs' )->asArrayString();
        return $this->proofs;
    }

    /**
     * @param array<int, string> $proofs
     */
    function setProofs( array $proofs = null ): void
    {
        if( !isset( $proofs ) )
            $proofs = [];
        $this->proofs = $proofs;
        $this->json->put( 'proofs', $proofs );
    }

    function bodyBytes(): string
    {
        return $this->bodyBytes;
    }

    function setBodyBytes( string $bodyBytes ): void
    {
        $this->bodyBytes = $bodyBytes;
    }
}
