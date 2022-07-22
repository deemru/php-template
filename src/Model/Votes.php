<?php declare( strict_types = 1 );

namespace wavesplatform\Model;

use wavesplatform\Common\Json;

class Votes extends Json
{
    function increase(): int { return $this->get( 'increase' )->asInt(); }
    function decrease(): int { return $this->get( 'decrease' )->asInt(); }
}   
