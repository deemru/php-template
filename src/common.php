<?php declare( strict_types = 1 );

namespace deemru;

require_once __DIR__ . '/../vendor/autoload.php';

use deemru\ABCode;
use Exception;

function base58Decode( string $string ): string
{
    $decoded = ABCode::base58()->decode( $string );
    if( $decoded === false )
        throw new Exception( __FUNCTION__ . ' failed to decode string: ' . $string );
    return $decoded;
}

function base58Encode( string $bytes ): string
{
    $encoded = ABCode::base58()->encode( $bytes );
    if( $encoded === false )
        throw new Exception( __FUNCTION__ . ' failed to encode bytes: ' . bin2hex( $bytes ) );
    return $encoded;
}
