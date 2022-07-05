<?php declare( strict_types = 1 );

namespace wavesplatform\Model;

class BlockHeaders extends JsonTemplate
{
    /**
     * @return array<int>
     */
    public function features(): array { return $this->get( 'features' )->asArrayInt(); }
    public function version(): int { return $this->get( 'version' )->asInt(); }
    public function timestamp(): int { return $this->get( 'timestamp' )->asInt(); }
    public function reference(): string { return $this->get( 'reference' )->asString(); }
    public function baseTarget(): int { return $this->get( 'nxt-consensus' )->asJson()->get( 'base-target' )->asInt(); }
    public function generationSignature(): string { return $this->get( 'nxt-consensus' )->asJson()->get( 'generation-signature' )->asString(); }
    public function transactionsRoot(): string { return $this->get( 'transactionsRoot' )->asString(); }
    public function id(): string { return $this->get( 'id' )->asString(); }
    public function desiredReward(): int { return $this->get( 'desiredReward' )->asInt(); }
    public function generator(): Address { return $this->get( 'generator' )->asAddress(); }
    public function signature(): string { return $this->get( 'signature' )->asString(); }
    public function size(): int { return $this->get( 'blocksize' )->asInt(); }
    public function transactionsCount(): int { return $this->get( 'transactionCount' )->asInt(); }
    public function height(): int { return $this->get( 'height' )->asInt(); }
    public function totalFee(): int { return $this->get( 'totalFee' )->asInt(); }
    public function reward(): int { return $this->get( 'reward' )->asInt(); }
    public function vrf(): string { return $this->get( 'VRF' )->asString(); }
}
