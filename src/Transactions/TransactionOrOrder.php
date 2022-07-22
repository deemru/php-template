<?php declare( strict_types = 1 );

namespace wavesplatform\Transactions;

use wavesplatform\Common\Json;
use wavesplatform\Account\PublicKey;
use wavesplatform\Model\AssetId;
use wavesplatform\Model\ChainId;
use wavesplatform\Model\Id;
use wavesplatform\Model\WavesConfig;

class TransactionOrOrder extends Json
{
    function id(): Id { return $this->get( 'id' )->asId(); }
    function version(): int { return $this->get( 'version' )->asInt(); }
    function sender(): PublicKey { return $this->get( 'senderPublicKey' )->asPublicKey(); }
    function chainId(): ChainId { return $this->getOr( 'chainId', WavesConfig::chainId() )->asChainId(); }
    function fee(): Amount { return Amount::of( $this->get( 'fee' )->asInt(), $this->getOr( 'feeAssetId', AssetId::WAVES_STRING )->asAssetId() ); }
    function timestamp(): int { return $this->get( 'timestamp' )->asInt(); }
    function proofs(): Json { return $this->get( 'proofs' )->asJson(); } // TODO: array<int, Proof>
}
