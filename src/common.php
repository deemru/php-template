<?php declare( strict_types = 1 );

namespace deemru;

require_once __DIR__ . '/../vendor/autoload.php';

use deemru\ABCode;
use Exception;

/**
 * Decodes binary data from base58 string
 *
 * @param string $string
 * @return string
 */
function base58Decode( string $string ): string
{
    $decoded = ABCode::base58()->decode( $string );
    if( $decoded === false )
        throw new Exception( __FUNCTION__ . ' failed to decode string: ' . $string, ErrCode::BASE58_DECODE );
    return $decoded;
}

/**
 * Encodes binary data to base58 string
 *
 * @param string $bytes
 * @return string
 */
function base58Encode( string $bytes ): string
{
    $encoded = ABCode::base58()->encode( $bytes );
    if( $encoded === false )
        // Unreachable for binary encodings
        throw new Exception( __FUNCTION__ . ' failed to encode bytes: ' . bin2hex( $bytes ), ErrCode::BASE58_ENCODE ); // @codeCoverageIgnore
    return $encoded;
}

/**
 * Json function constructor
 *
 * @param array<int|string, mixed> $json
 * @return Json
 */
function asJson( array $json ): Json
{
    return new Json( $json );
}

function asValue( mixed $value ): Value
{
    return new Value( $value );
}
