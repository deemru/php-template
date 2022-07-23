<?php declare( strict_types = 1 );

namespace wavesplatform\Model;

use wavesplatform\Common\JsonBase;

class ScriptDetails extends JsonBase
{
    const EMPTY = [ 'script' => '', 'scriptComplexity' => 0 ];

    function script(): string { return $this->json->get( 'script' )->asString(); } // TODO: Base64String
    function complexity(): int { return $this->json->get( 'scriptComplexity' )->asInt(); }
}
