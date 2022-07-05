<?php declare( strict_types = 1 );

namespace wavesplatform\Model;

class ScriptInfo extends JsonTemplate
{
    public function script(): string { return $this->get( 'script' )->asString(); }
    public function complexity(): int { return $this->get( 'complexity' )->asInt(); }
    public function verifierComplexity(): int { return $this->get( 'verifierComplexity' )->asInt(); }
    public function extraFee(): int { return $this->get( 'extraFee' )->asInt(); }
    /**
     * Gets a map of callable functions with their complexities
     *
     * @return array<string, int>
     */
    public function callableComplexities(): array { return $this->get( 'callableComplexities' )->asMapStringInt(); }
}
