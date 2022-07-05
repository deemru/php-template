<?php declare( strict_types = 1 );

namespace wavesplatform\Model;

use wavesplatform\Util\Functions;

class Address
{
    private Base58String $base58String;

    static public function fromString( string $encoded ): Address
    {
        $address = new Address;
        $address->base58String = Base58String::fromString( $encoded, true );
        return $address;
    }

    static public function fromBytes( string $bytes ): Address
    {
        $address = new Address;
        $address->base58String = Base58String::fromBytes( $bytes );
        return $address;
    }

    public function chainId(): string
    {
        return $this->base58String->bytes()[1];
    }

    public function bytes(): string
    {
        return $this->base58String->bytes();
    }

    public function encoded(): string
    {
        return $this->base58String->encoded();
    }

    public function toString(): string
    {
        return $this->base58String->encoded();
    }
}
