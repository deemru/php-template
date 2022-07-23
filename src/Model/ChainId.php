<?php declare( strict_types = 1 );

namespace wavesplatform\Model;

use Exception;
use wavesplatform\Common\ExceptionCode;

class ChainId
{
    const MAINNET = 'W';
    const TESTNET = 'T';
    const STAGENET = 'S';
    const PRIVATE = 'R';

    private string $chainId;

    private function __construct(){}

    static function fromInt( int $int ): ChainId
    {
        if( $int < 0 || $int > 255 )
            throw new Exception( __FUNCTION__ . ' bad chainId value: ' . $int, ExceptionCode::BAD_CHAINID );
        $chainId = new ChainId;
        $chainId->chainId = chr( $int );
        return $chainId;
    }

    static function fromString( string $string ): ChainId
    {
        if( strlen( $string ) !== 1 )
            throw new Exception( __FUNCTION__ . ' bad chainId value: ' . strlen( $string ), ExceptionCode::BAD_CHAINID );
        $chainId = new ChainId;
        $chainId->chainId = $string;
        return $chainId;
    }

    static function MAINNET(): ChainId { return ChainId::fromString( ChainId::MAINNET ); }
    static function TESTNET(): ChainId { return ChainId::fromString( ChainId::TESTNET ); }
    static function STAGENET(): ChainId { return ChainId::fromString( ChainId::STAGENET ); }
    static function PRIVATE(): ChainId { return ChainId::fromString( ChainId::PRIVATE ); }

    function asInt(): int
    {
        return ord( $this->chainId );
    }

    function asString(): string
    {
        return $this->chainId;
    }
}
