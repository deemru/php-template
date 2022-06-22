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
 * Gets an integer value from a mixed value
 *
 * @param mixed $value
 * @return int
 */
function asInt( mixed $value ): int
{
    if( !is_int( $value ) )
        throw new Exception( __FUNCTION__ . ' failed to detect integer at `' . $value . '`', ErrCode::INT_EXPECTED );
    return $value;
}

/**
 * Gets an integer value from a key-value array by its key
 *
 * @param array<mixed, mixed> $array
 * @param mixed $key
 * @return int
 */
function getInt( array $array, mixed $key ): int
{
    if( !isset( $array[$key] ) )
        throw new Exception( __FUNCTION__ . ' failed to find key `' . $key . '`', ErrCode::KEY_MISSING );
    return asInt( $array[$key] );
}

/**
 * Gets a string value from a mixed value
 *
 * @param mixed $value
 * @return string
 */
function asString( mixed $value ): string
{
    if( !is_string( $value ) )
        throw new Exception( __FUNCTION__ . ' failed to detect integer at `' . $value . '`', ErrCode::STRING_EXPECTED );
    return $value;
}

/**
 * Gets a string value from a key-value array by its key
 *
 * @param array<mixed, mixed> $array
 * @param mixed $key
 * @return string
 */
function getString( array $array, mixed $key ): string
{
    if( !isset( $array[$key] ) )
        throw new Exception( __FUNCTION__ . ' failed to find key `' . $key . '`', ErrCode::KEY_MISSING );
    return asString( $array[$key] );
}
