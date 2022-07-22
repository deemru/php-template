<?php declare( strict_types = 1 );

namespace wavesplatform\Model;

use wavesplatform\Common\Json;

class ArgMeta extends Json
{
    function name(): string { return $this->get( 'name' )->asString(); }
    function type(): string { return $this->get( 'type' )->asString(); }
}
