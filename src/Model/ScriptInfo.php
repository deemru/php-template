<?php declare( strict_types = 1 );

namespace wavesplatform\Model;

use wavesplatform\Common\Json;

class ScriptInfo extends Json
{
    function script(): string { return $this->get( 'script' )->asString(); }
    function complexity(): int { return $this->get( 'complexity' )->asInt(); }
    function verifierComplexity(): int { return $this->get( 'verifierComplexity' )->asInt(); }
    function extraFee(): int { return $this->get( 'extraFee' )->asInt(); }
    /**
     * Gets a map of callable functions with their complexities
     *
     * @return array<string, int>
     */
    function callableComplexities(): array { return $this->get( 'callableComplexities' )->asMapStringInt(); }
}
