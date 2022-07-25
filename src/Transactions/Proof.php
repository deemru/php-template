<?php declare( strict_types = 1 );

namespace wavesplatform\Transactions;

use wavesplatform\Common\Base58String;
use wavesplatform\Common\Value;

class Proof
{
    const LATEST_VERSION = 1;
    const BYTE_LENGTH = 64;

    private Base58String $proof;

    private function __construct(){}

    /**
     * @return array<int, Proof>
     */
    function emptyList(): array
    {
        return Value::asValue( [] )->asArrayProof();
    }

    static function fromString( string $encoded ): Proof
    {
        $proof = new Proof;
        $proof->proof = Base58String::fromString( $encoded );
        return $proof;
    }

    function proof(): Base58String
    {
        return $this->proof;
    }
}
