<?php

require_once __DIR__ . '/../vendor/autoload.php';

function prepare()
{
    define( 'DO_LOCAL_DEBUG', !defined( 'PHPUNIT_RUNNING' ) );

    if( file_exists( 'config.php' ) )
        require_once 'config.php';

    $wavesConfig = getenv( 'WAVES_CONFIG' );
    var_dump( $wavesConfig );
    if( is_string( $wavesConfig ) )
    {
        //echo $wavesConfig;
        $wavesConfig = json_decode( hex2bin( $wavesConfig ), true );
        if( $wavesConfig === false )
            return;

        foreach( $wavesConfig as $key => $value )
            define( $key, $value );
    }
}

prepare();
