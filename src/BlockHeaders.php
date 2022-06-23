<?php declare( strict_types = 1 );

namespace deemru;

require_once __DIR__ . '/common.php';

use Exception;

class BlockHeaders
{
    private Json $json;

    public function __construct( Json $json )
    {
        $this->json = $json;
    }

    /**
     * @return array<int>
     */
    public function features(): array { return $this->json->get( 'features' )->asArrayInt(); }
    public function version(): int { return $this->json->get( 'version' )->asInt(); }
    public function timestamp(): int { return $this->json->get( 'timestamp' )->asInt(); }
    public function reference(): string { return $this->json->get( 'reference' )->asString(); }
    public function baseTarget(): int { return $this->json->get( 'nxt-consensus' )->asJson()->get( 'base-target' )->asInt(); }
    public function generationSignature(): string { return $this->json->get( 'nxt-consensus' )->asJson()->get( 'generation-signature' )->asString(); }
    public function transactionsRoot(): string { return $this->json->get( 'transactionsRoot' )->asString(); }
    public function id(): string { return $this->json->get( 'id' )->asString(); }
    public function desiredReward(): int { return $this->json->get( 'desiredReward' )->asInt(); }
    public function generator(): Address { return $this->json->get( 'generator' )->asAddress(); }
    public function signature(): string { return $this->json->get( 'signature' )->asString(); }
    public function size(): int { return $this->json->get( 'blocksize' )->asInt(); }
    public function transactionsCount(): int { return $this->json->get( 'transactionCount' )->asInt(); }
    public function height(): int { return $this->json->get( 'height' )->asInt(); }
    public function totalFee(): int { return $this->json->get( 'totalFee' )->asInt(); }
    public function reward(): int { return $this->json->get( 'reward' )->asInt(); }
    public function vrf(): string { return $this->json->get( 'VRF' )->asString(); }
}
