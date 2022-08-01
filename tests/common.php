<?php

require_once __DIR__ . '/../vendor/autoload.php';

function prepare(): void
{
    define( 'DO_LOCAL_DEBUG', !defined( 'PHPUNIT_RUNNING' ) );

    if( file_exists( 'config.php' ) )
        require_once 'config.php';

    $wavesConfig = getenv( 'WAVES_CONFIG' );
    var_dump( $wavesConfig );
    if( !is_string( $wavesConfig ) )
        return;

    //echo $wavesConfig; // print value

    $wavesConfig = hex2bin( $wavesConfig );
    if( !is_string( $wavesConfig ) )
        return;

    $wavesConfig = json_decode( hex2bin( $wavesConfig ), true );
    if( $wavesConfig === false )
        return;

    foreach( $wavesConfig as $key => $value )
        define( $key, $value );
}

prepare();
