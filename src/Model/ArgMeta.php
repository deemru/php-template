<?php declare( strict_types = 1 );

namespace wavesplatform\Model;

class ArgMeta extends JsonTemplate
{
    function name(): string { return $this->get( 'name' )->asString(); }
    function type(): string { return $this->get( 'type' )->asString(); }
}
