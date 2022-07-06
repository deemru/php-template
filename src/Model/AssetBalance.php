<?php declare( strict_types = 1 );

namespace wavesplatform\Model;

class AssetBalance extends JsonTemplate
{
    function assetId(): AssetId { return $this->get( 'assetId' )->asAssetId(); }
    function balance(): int { return $this->get( 'balance' )->asInt(); }
    function isReissuable(): bool { return $this->get( 'reissuable' )->asBoolean(); }
    function quantity(): int { return $this->get( 'quantity' )->asInt(); }
    function minSponsoredAssetFee(): int { return $this->get( 'minSponsoredAssetFee' )->asInt(); }
    function sponsorBalance(): int { return $this->get( 'sponsorBalance' )->asInt(); }
    function issueTransaction(): Json { return $this->get( 'issueTransaction' )->asJson(); } // TODO: Transaction
}
