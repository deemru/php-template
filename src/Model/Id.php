<?php declare( strict_types = 1 );

namespace wavesplatform\Model;

use Exception;
use wavesplatform\ExceptionCode;

class Id
{
    const BYTE_LENGTH = 32;

    private Base58String $base58String;

    private function __construct(){}

    static function fromString( string $encoded ): Id
    {
        $id = new Id;
        $id->base58String = Base58String::fromString( $encoded );
        $bytes = $id->base58String->bytes();
        if( strlen( $bytes ) !== Id::BYTE_LENGTH )
            throw new Exception( __FUNCTION__ . ' bad id length: ' . strlen( $bytes ), ExceptionCode::BAD_ASSET );
        return $id;
    }

    static function fromBytes( string $bytes ): Id
    {
        if( strlen( $bytes ) !== Id::BYTE_LENGTH )
            throw new Exception( __FUNCTION__ . ' bad id length: ' . strlen( $bytes ), ExceptionCode::BAD_ASSET );
        $id = new Id;
        $id->base58String = Base58String::fromBytes( $bytes );
        return $id;
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
        return $this->encoded();
    }
}
