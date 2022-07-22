<?php declare( strict_types = 1 );

namespace wavesplatform\Model;

use wavesplatform\Common\JsonTemplate;

class BalanceDetails extends JsonTemplate
{
    function address(): string { return $this->get( 'address' )->asString(); }
    function available(): int { return $this->get( 'available' )->asInt(); }
    function regular(): int { return $this->get( 'regular' )->asInt(); }
    function generating(): int { return $this->get( 'generating' )->asInt(); }
    function effective(): int { return $this->get( 'effective' )->asInt(); }
}
