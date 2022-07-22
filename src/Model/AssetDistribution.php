<?php declare( strict_types = 1 );

namespace wavesplatform\Model;

use wavesplatform\Common\Json;

class AssetDistribution extends Json
{
    /**
     * @return array<string, int>
     */
    function items(): array { return $this->get( 'items' )->asMapStringInt(); }
    function lastItem(): string { return $this->get( 'lastItem' )->asString(); }
    function hasNext(): bool { return $this->getOr( 'hasNext', false )->asBoolean(); }
}
