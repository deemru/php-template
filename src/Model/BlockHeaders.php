<?php declare( strict_types = 1 );

namespace wavesplatform\Model;

use wavesplatform\Account\Address;
use wavesplatform\Common\Json;

class BlockHeaders extends Json
{
    /**
     * @return array<int>
     */
    function features(): array { return $this->get( 'features' )->asArrayInt(); }
    function version(): int { return $this->get( 'version' )->asInt(); }
    function timestamp(): int { return $this->get( 'timestamp' )->asInt(); }
    function reference(): string { return $this->get( 'reference' )->asString(); }
    function baseTarget(): int { return $this->get( 'nxt-consensus' )->asJson()->get( 'base-target' )->asInt(); }
    function generationSignature(): string { return $this->get( 'nxt-consensus' )->asJson()->get( 'generation-signature' )->asString(); }
    function transactionsRoot(): string { return $this->get( 'transactionsRoot' )->asString(); }
    function id(): Id { return $this->get( 'id' )->asId(); }
    function desiredReward(): int { return $this->get( 'desiredReward' )->asInt(); }
    function generator(): Address { return $this->get( 'generator' )->asAddress(); }
    function signature(): string { return $this->get( 'signature' )->asString(); }
    function size(): int { return $this->get( 'blocksize' )->asInt(); }
    function transactionsCount(): int { return $this->get( 'transactionCount' )->asInt(); }
    function height(): int { return $this->get( 'height' )->asInt(); }
    function totalFee(): int { return $this->get( 'totalFee' )->asInt(); }
    function reward(): int { return $this->get( 'reward' )->asInt(); }
    function vrf(): string { return $this->get( 'VRF' )->asString(); }
}
