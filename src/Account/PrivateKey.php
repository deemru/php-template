<?php declare( strict_types = 1 );

namespace wavesplatform\Account;

use deemru\WavesKit;
use wavesplatform\Common\Base58String;

class PrivateKey
{
    const LENGTH = 32;

    private Base58String $key;

    private function __construct(){}

    static function fromSeed( string $seed, int $nonce = 0 ): PrivateKey
    {
        $privateKey = new PrivateKey;
        $privateKey->key = Base58String::fromBytes( ( new WavesKit )->getPrivateKey( true, $seed, pack( 'N', $nonce ) ) );
        return $privateKey;
    }

    static function fromBytes( string $key ): PrivateKey
    {
        $privateKey = new PrivateKey;
        $privateKey->key = Base58String::fromBytes( $key );
        return $privateKey;
    }

    static function fromString( string $key ): PrivateKey
    {
        $privateKey = new PrivateKey;
        $privateKey->key = Base58String::fromString( $key );
        return $privateKey;
    }

    function bytes(): string
    {
        return $this->key->bytes();
    }

    function toString(): string
    {
        return $this->key->toString();
    }
}
