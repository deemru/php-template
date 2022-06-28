<?php declare( strict_types = 1 );

namespace deemru;

require_once __DIR__ . '/common.php';

class Address
{
    private string $bytes;
    private string $encoded;

    private function __construct( string $bytes, string $encoded )
    {
        $this->bytes = $bytes;
        $this->encoded = $encoded;
    }

    static public function fromString( string $encoded ): Address
    {
        $bytes = base58Decode( $encoded );
        return new Address( $bytes, $encoded );
    }

    static public function fromBytes( string $bytes ): Address
    {
        $encoded = base58Encode( $bytes );
        return new Address( $bytes, $encoded );
    }

    public function chainId(): string
    {
        return $this->bytes[1];
    }

    public function bytes(): string
    {
        return $this->bytes;
    }

    public function encoded(): string
    {
        return $this->encoded;
    }

    public function toString(): string
    {
        return $this->encoded;
    }
}
