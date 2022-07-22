<?php declare( strict_types = 1 );

namespace wavesplatform\Model;

use wavesplatform\Common\JsonTemplate;

class LeaseInfo extends JsonTemplate
{
    function id(): Id { return $this->get( 'id' )->asId(); }
    function originTransactionId(): Id { return $this->get( 'originTransactionId' )->asId(); }
    function sender(): Address { return $this->get( 'sender' )->asAddress(); }
    function recipient(): string { return $this->get( 'recipient' )->asString(); } // TODO: Recipient
    function amount(): int { return $this->get( 'amount' )->asInt(); }
    function height(): int { return $this->get( 'height' )->asInt(); }
    function status(): int
    {
        $status = $this->getOr( 'status', LeaseStatus::UNKNOWN_S )->asString();
        switch( $status )
        {
            case LeaseStatus::ACTIVE_S: return LeaseStatus::ACTIVE;
            case LeaseStatus::CANCELED_S: return LeaseStatus::CANCELED;
            default: return LeaseStatus::UNKNOWN;
        }
    }
    function cancelHeight(): int { return $this->get( 'cancelHeight' )->asInt(); }
    function cancelTransactionId(): Id { return $this->get( 'cancelTransactionId' )->asId(); }
}
