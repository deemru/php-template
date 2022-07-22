<?php declare( strict_types = 1 );

namespace wavesplatform\Model;

use wavesplatform\Account\Address;
use wavesplatform\Common\Json;

class AssetDetails extends Json
{
    function assetId(): AssetId { return $this->get( 'assetId' )->asAssetId(); }
    function issueHeight(): int { return $this->get( 'issueHeight' )->asInt(); }
    function issueTimestamp(): int { return $this->get( 'issueTimestamp' )->asInt(); }
    function issuer(): Address { return $this->get( 'issuer' )->asAddress(); }
    function issuerPublicKey(): string { return $this->get( 'issuerPublicKey' )->asString(); } // TODO: PublicKey
    function name(): string { return $this->get( 'name' )->asString(); }
    function description(): string { return $this->get( 'description' )->asString(); }
    function decimals(): int { return $this->get( 'decimals' )->asInt(); }
    function isReissuable(): bool { return $this->get( 'reissuable' )->asBoolean(); }
    function quantity(): int { return $this->get( 'quantity' )->asInt(); }
    function isScripted(): bool { return $this->get( 'scripted' )->asBoolean(); }
    function minSponsoredAssetFee(): int { return $this->getOr( 'minSponsoredAssetFee', 0 )->asInt(); }
    function originTransactionId(): Id { return $this->get( 'originTransactionId' )->asId(); }
    function scriptDetails(): ScriptDetails { return $this->getOr( 'scriptDetails', ScriptDetails::EMPTY )->asJson()->asScriptDetails(); }
}   
