<?php declare( strict_types = 1 );

namespace wavesplatform\Model;

use Exception;
use wavesplatform\Common\ExceptionCode;
use wavesplatform\Common\Base58String;

class AssetId
{
    const BYTE_LENGTH = 32;
    const WAVES_STRING = "WAVES";

    private Base58String $base58String;

    private function __construct(){}

    static function WAVES(): AssetId
    {
        return new AssetId;
    }

    static function fromString( string $encoded ): AssetId
    {
        if( strtoupper( $encoded ) === AssetId::WAVES_STRING )
            return AssetId::WAVES();

        $assetId = new AssetId;
        $assetId->base58String = Base58String::fromString( $encoded );
        return $assetId;
    }

    static function fromBytes( string $bytes ): AssetId
    {
        if( $bytes === '' )
            return AssetId::WAVES();

        if( strlen( $bytes ) !== AssetId::BYTE_LENGTH )
            throw new Exception( __FUNCTION__ . ' bad asset length: ' . strlen( $bytes ), ExceptionCode::BAD_ASSET );
        $assetId = new AssetId;
        $assetId->base58String = Base58String::fromBytes( $bytes );
        return $assetId;
    }

    function isWaves(): bool
    {
        return !isset( $this->base58String );
    }

    function bytes(): string
    {
        if( $this->isWaves() )
            return '';
        $bytes = $this->base58String->bytes();
        if( strlen( $bytes ) !== AssetId::BYTE_LENGTH )
            throw new Exception( __FUNCTION__ . ' bad asset length: ' . strlen( $bytes ), ExceptionCode::BAD_ASSET );
        return $bytes;
    }

    function encoded(): string
    {
        return $this->isWaves() ? AssetId::WAVES_STRING : $this->base58String->encoded();
    }

    function toString(): string
    {
        return $this->encoded();
    }
}
