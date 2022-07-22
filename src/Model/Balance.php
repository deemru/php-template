<?php declare( strict_types = 1 );

namespace wavesplatform\Model;

use wavesplatform\Common\Json;

class Balance extends Json
{
    function getAddress(): string { return $this->get( 'id' )->asString(); }
    function getBalance(): int { return $this->get( 'balance' )->asInt(); }
}
