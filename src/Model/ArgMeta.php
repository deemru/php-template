<?php declare( strict_types = 1 );

namespace wavesplatform\Model;

use wavesplatform\Common\JsonBase;

class ArgMeta extends JsonBase
{
    function name(): string { return $this->json->get( 'name' )->asString(); }
    function type(): string { return $this->json->get( 'type' )->asString(); }
}
