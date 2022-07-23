<?php declare( strict_types = 1 );

namespace wavesplatform\Transactions;

use wavesplatform\Common\Json;
use wavesplatform\Common\JsonBase;
use wavesplatform\Account\PublicKey;
use wavesplatform\Model\AssetId;
use wavesplatform\Model\ChainId;
use wavesplatform\Model\Id;
use wavesplatform\Model\WavesConfig;

class TransactionOrOrder extends JsonBase
{
    private Id $id;
    private int $version;
    private ChainId $chainId;
    private PublicKey $sender;
    private int $timestamp;
    private Amount $fee;
    private int $extraFee;
    private Json $proofs;
    private string $bodyBytes;

    function id(): Id
    {
        if( !isset( $this->id ) )
            $this->id = $this->json->get( 'id' )->asId();
        return $this->id;
    }
    
    function version(): int
    {
        if( !isset( $this->version ) )
            $this->version = $this->json->get( 'version' )->asInt();
        return $this->version;
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
    
    function sender(): PublicKey
    {
        if( !isset( $this->sender ) )
            $this->sender = $this->json->get( 'senderPublicKey' )->asPublicKey();
        return $this->sender;
    }

    function timestamp(): int
    {
        if( !isset( $this->timestamp ) )
            $this->timestamp = $this->json->get( 'timestamp' )->asInt();
        return $this->timestamp;
    }
    
    function fee(): Amount
    {
        if( !isset( $this->fee ) )
            $this->fee = Amount::of( $this->json->get( 'fee' )->asInt(), $this->json->getOr( 'feeAssetId', AssetId::WAVES_STRING )->asAssetId() );
        return $this->fee;
    }

    function proofs(): Json 
    {
        return $this->json->get( 'proofs' )->asJson();// TODO: array<int, Proof>
    }

    function setFee( Amount $fee )
    {
        $this->fee = $fee;
        $this->json->put( 'fee', $fee->value() );
        $this->json->put( 'feeAssetId', $fee->assetId()->toString() );
    }
}
