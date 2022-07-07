<?php declare( strict_types = 1 );

namespace wavesplatform\Model;

class TransactionOrOrder extends JsonTemplate
{
    function id(): Id { return $this->get( 'id' )->asId(); }
    function version(): int { return $this->get( 'version' )->asInt(); }
    function sender(): Address { return $this->get( 'sender' )->asAddress(); } // TODO: PublicKey
    function senderPublicKey(): string { return $this->get( 'senderPublicKey' )->asString(); } // TODO: PublicKey
    function chainId(): chainId { return $this->getOr( 'chainId', WavesConfig::chainId() )->asChainId(); }
    function fee(): int { return $this->get( 'fee' )->asInt(); } // TODO: Amount
    function feeAssetId(): string { return $this->get( 'feeAssetId' )->asString(); } // TODO: Amount
    function timestamp(): int { return $this->get( 'timestamp' )->asInt(); }
    function proofs(): Json { return $this->get( 'proofs' )->asJson(); } // TODO: array<int, Proof>
}
