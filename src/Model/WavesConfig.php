<?php declare( strict_types = 1 );

namespace wavesplatform\Model;

class WavesConfig
{
    private static $chainId = ChainId::MAINNET;

    static function chainId( string $chainId = '' ): string
    {
        if( strlen( $chainId ) === 1 )
            WavesConfig::$chainId = $chainId;
        return WavesConfig::$chainId;
    }
}
