<?php declare( strict_types = 1 );

namespace wavesplatform\Model;

use wavesplatform\Common\JsonTemplate;

class TransactionStatus extends JsonTemplate
{
    function id(): Id { return $this->get( 'id' )->asId(); }
    function status(): int { return $this->get( 'status' )->asStatus(); }
    function applicationStatus(): int { return $this->get( 'applicationStatus' )->asApplicationStatus(); }
    function height(): int { return $this->getOr( 'height', 0 )->asInt(); }
    function confirmations(): int { return $this->getOr( 'confirmations', 0 )->asInt(); }
}
