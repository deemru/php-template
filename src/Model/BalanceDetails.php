<?php declare( strict_types = 1 );

namespace wavesplatform\Model;

use wavesplatform\Common\JsonBase;

class BalanceDetails extends JsonBase
{
    function address(): string { return $this->json->get( 'address' )->asString(); }
    function available(): int { return $this->json->get( 'available' )->asInt(); }
    function regular(): int { return $this->json->get( 'regular' )->asInt(); }
    function generating(): int { return $this->json->get( 'generating' )->asInt(); }
    function effective(): int { return $this->json->get( 'effective' )->asInt(); }
}
