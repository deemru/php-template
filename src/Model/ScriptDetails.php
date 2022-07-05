<?php declare( strict_types = 1 );

namespace wavesplatform\Model;

class ScriptDetails extends JsonTemplate
{
    public function script(): string { return $this->get( 'script' )->asString(); } // TODO: Base64String
    public function complexity(): int { return $this->get( 'complexity' )->asInt(); }
}
