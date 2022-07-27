<?php declare( strict_types = 1 );

namespace wavesplatform\Transactions;

use Exception;
use wavesplatform\Account\Address;
use wavesplatform\Common\ExceptionCode;
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

    static function fromAddressOrAlias( string $addressOrAlias ): Recipient
    {
        if( strlen( $addressOrAlias ) === Address::STRING_LENGTH )
            return Recipient::fromAddress( Address::fromString( $addressOrAlias ) );
        try
        {
            return Recipient::fromAlias( Alias::fromFullAlias( $addressOrAlias ) );
        }
        catch( Exception $e )
        {
            if( $e->getCode() !== ExceptionCode::BAD_ALIAS )
                throw $e;

            return Recipient::fromAlias( Alias::fromString( $addressOrAlias ) );
        }
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

    function address(): Address
    {
        return $this->address;
    }

    function alias(): Alias
    {
        return $this->alias;
    }
}
