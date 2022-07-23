<?php declare( strict_types = 1 );

namespace wavesplatform\Model;

use wavesplatform\Common\Json;
use wavesplatform\Common\JsonBase;

class AssetBalance extends JsonBase
{
    function assetId(): AssetId { return $this->json->get( 'assetId' )->asAssetId(); }
    function balance(): int { return $this->json->get( 'balance' )->asInt(); }
    function isReissuable(): bool { return $this->json->get( 'reissuable' )->asBoolean(); }
    function quantity(): int { return $this->json->get( 'quantity' )->asInt(); }
    function minSponsoredAssetFee(): int { return $this->json->getOr( 'minSponsoredAssetFee', 0 )->asInt(); }
    function sponsorBalance(): int { return $this->json->getOr( 'sponsorBalance', 0 )->asInt(); }
    function issueTransaction(): Json { return $this->json->get( 'issueTransaction' )->asJson(); } // TODO: Transaction
}
