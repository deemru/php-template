<?php declare( strict_types = 1 );

namespace wavesplatform\Model;

use Exception;
use wavesplatform\ExceptionCode;

class Address
{
    const BYTE_LENGTH = 26;

    private Base58String $base58String;

    private function __construct(){}

    static function fromString( string $encoded ): Address
    {
        $address = new Address;
        $address->base58String = Base58String::fromString( $encoded );
        return $address;
    }

    static function fromBytes( string $bytes ): Address
    {
        if( strlen( $bytes ) !== Address::BYTE_LENGTH )
            throw new Exception( __FUNCTION__ . ' bad address length: ' . strlen( $bytes ), ExceptionCode::BAD_ADDRESS );
        $address = new Address;
        $address->base58String = Base58String::fromBytes( $bytes );
        return $address;
    }

    function chainId(): string
    {
        return $this->bytes()[1];
    }

    function bytes(): string
    {
        $bytes = $this->base58String->bytes();
        if( strlen( $bytes ) !== Address::BYTE_LENGTH )
            throw new Exception( __FUNCTION__ . ' bad address length: ' . strlen( $bytes ), ExceptionCode::BAD_ADDRESS );
        return $bytes;
    }

    function encoded(): string
    {
        return $this->base58String->encoded();
    }

    function toString(): string
    {
        return $this->encoded();
    }
}
