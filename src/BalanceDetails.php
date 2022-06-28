<?php declare( strict_types = 1 );

namespace deemru;

require_once __DIR__ . '/common.php';

class BalanceDetails extends JsonTemplate
{
    public function address(): string { return $this->get( 'address' )->asString(); }
    public function available(): int { return $this->get( 'available' )->asInt(); }
    public function regular(): int { return $this->get( 'regular' )->asInt(); }
    public function generating(): int { return $this->get( 'generating' )->asInt(); }
    public function effective(): int { return $this->get( 'effective' )->asInt(); }
}
