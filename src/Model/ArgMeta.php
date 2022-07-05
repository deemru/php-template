<?php declare( strict_types = 1 );

namespace wavesplatform\Model;

class ArgMeta extends JsonTemplate
{
    public function name(): string { return $this->get( 'name' )->asString(); }
    public function type(): string { return $this->get( 'type' )->asString(); }
}
