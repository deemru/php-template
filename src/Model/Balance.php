<?php declare( strict_types = 1 );

namespace wavesplatform\Model;

class Balance extends JsonTemplate
{
    public function getAddress(): string { return $this->get( 'id' )->asString(); }
    public function getBalance(): int { return $this->get( 'balance' )->asInt(); }
}
