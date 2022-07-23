<?php declare( strict_types = 1 );

namespace wavesplatform\Model;

use wavesplatform\Common\JsonBase;

class ScriptInfo extends JsonBase
{
    function script(): string { return $this->json->get( 'script' )->asString(); }
    function complexity(): int { return $this->json->get( 'complexity' )->asInt(); }
    function verifierComplexity(): int { return $this->json->get( 'verifierComplexity' )->asInt(); }
    function extraFee(): int { return $this->json->get( 'extraFee' )->asInt(); }
    /**
     * Gets a map of callable functions with their complexities
     *
     * @return array<string, int>
     */
    function callableComplexities(): array { return $this->json->get( 'callableComplexities' )->asMapStringInt(); }
}
