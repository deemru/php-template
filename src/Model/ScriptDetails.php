<?php declare( strict_types = 1 );

namespace wavesplatform\Model;

class ScriptDetails extends JsonTemplate
{
    const EMPTY = [ 'script' => '', 'scriptComplexity' => 0 ];

    function script(): string { return $this->get( 'script' )->asString(); } // TODO: Base64String
    function complexity(): int { return $this->get( 'scriptComplexity' )->asInt(); }
}
