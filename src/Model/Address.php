<?php declare( strict_types = 1 );

namespace wavesplatform\Model;

class Address
{
    private Base58String $base58String;

    static function fromString( string $encoded ): Address
    {
        $address = new Address;
        $address->base58String = Base58String::fromString( $encoded, true );
        return $address;
    }

    static function fromBytes( string $bytes ): Address
    {
        $address = new Address;
        $address->base58String = Base58String::fromBytes( $bytes );
        return $address;
    }

    function chainId(): string
    {
        return $this->base58String->bytes()[1];
    }

    function bytes(): string
    {
        return $this->base58String->bytes();
    }

    function encoded(): string
    {
        return $this->base58String->encoded();
    }

    function toString(): string
    {
        return $this->base58String->encoded();
    }
}
