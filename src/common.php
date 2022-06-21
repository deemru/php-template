<?php declare( strict_types = 1 );

namespace deemru;

require_once __DIR__ . '/../vendor/autoload.php';

use deemru\ABCode;
use Exception;

function base58Decode( string $string ): string
{
    $decoded = ABCode::base58()->decode( $string );
    if( $decoded === false )
        throw new Exception( __FUNCTION__ . ' failed to decode string: ' . $string, ErrCode::BASE58_DECODE );
    return $decoded;
}

function base58Encode( string $bytes ): string
{
    $encoded = ABCode::base58()->encode( $bytes );
    if( $encoded === false )
        // Unreachable for binary encodings
        throw new Exception( __FUNCTION__ . ' failed to encode bytes: ' . bin2hex( $bytes ), ErrCode::BASE58_ENCODE ); // @codeCoverageIgnore
    return $encoded;
}

/**
 * Gets an integer value from a key-value array by its key
 *
 * @param array<mixed, mixed> $array
 * @param string $key
 * @return int
 */
function asInt( array $array, string $key ): int
{
    if( !isset( $array[$key] ) )
        throw new Exception( __FUNCTION__ . ' failed to find key `' . $key . '`', ErrCode::KEY_MISSING );
    $int = $array[$key];
    if( !is_int( $int ) )
        throw new Exception( __FUNCTION__ . ' failed to detect integer at `' . $key . '`', ErrCode::INT_EXPECTED );
    return $int;
}
