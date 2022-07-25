<?php declare( strict_types = 1 );

namespace wavesplatform\Model;

class WavesConfig
{
    private static ChainId $chainId;

    static function chainId( chainId $chainId = null ): ChainId
    {
        if( isset( $chainId ) )
            WavesConfig::$chainId = $chainId;
        else if( !isset( WavesConfig::$chainId ) )
            WavesConfig::$chainId = ChainId::MAINNET();
        return WavesConfig::$chainId;
    }
}
