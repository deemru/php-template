<?php declare( strict_types = 1 );

namespace wavesplatform\Account;

use deemru\WavesKit;
use wavesplatform\Common\Base58String;
use wavesplatform\Model\ChainId;

class PublicKey
{
    const BYTES_LENGTH = 32;
    const ETH_BYTES_LENGTH = 64;

    private Base58String $key;
    private Address $address;

    private function __construct(){}

    static function fromBytes( string $key ): PublicKey
    {
        $publicKey = new PublicKey;
        $publicKey->key = Base58String::fromBytes( $key );
        return $publicKey;
    }

    static function fromString( string $key ): PublicKey
    {
        $publicKey = new PublicKey;
        $publicKey->key = Base58String::fromString( $key );
        return $publicKey;
    }

    static function fromPrivateKey( PrivateKey $key ): PublicKey
    {
        $publicKey = new PublicKey;
        $wk = new WavesKit;
        $wk->setPrivateKey( $key->bytes(), true );
        $publicKey->key = Base58String::fromBytes( $wk->getPublicKey( true ) );
        return $publicKey;
    }

    function address( ChainId $chainId = null ): Address
    {
        if( !isset( $this->address ) )
            $this->address = Address::fromPublicKey( $this, $chainId );
        return $this->address;
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
