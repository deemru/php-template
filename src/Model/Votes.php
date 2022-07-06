<?php declare( strict_types = 1 );

namespace wavesplatform\Model;

class Votes extends JsonTemplate
{
    function increase(): int { return $this->get( 'increase' )->asInt(); }
    function decrease(): int { return $this->get( 'decrease' )->asInt(); }
}   
