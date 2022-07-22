<?php declare( strict_types = 1 );

namespace wavesplatform\Model;

use wavesplatform\Common\JsonTemplate;

class Balance extends JsonTemplate
{
    function getAddress(): string { return $this->get( 'id' )->asString(); }
    function getBalance(): int { return $this->get( 'balance' )->asInt(); }
}
