<?php declare( strict_types = 1 );

namespace wavesplatform\Transactions;

use wavesplatform\Account\Address;
use wavesplatform\Model\Alias;

class Recipient
{
    private Address $address;
    private Alias $alias;

    private function __construct(){}

    static function fromAddress( Address $address ): Recipient
    {
        $recipient = new Recipient;
        $recipient->address = $address;
        return $recipient;
    }

    static function fromAlias( Alias $alias ): Recipient
    {
        $recipient = new Recipient;
        $recipient->alias = $alias;
        return $recipient;
    }

    function isAlias(): bool
    {
        return isset( $this->alias );
    }

    function bytes(): string
    {
        if( $this->isAlias() )
            return $this->alias->bytes();
        return $this->address->bytes();
    }

    function toString(): string
    {
        if( $this->isAlias() )
            return $this->alias->toString();
        return $this->address->toString();
    }
}
