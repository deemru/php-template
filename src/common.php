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

function asInt( array $array, string $field ): int
{
    if( !isset( $array[$field] ) )
        throw new Exception( __FUNCTION__ . ' failed to find field `' . $field . '`', ErrCode::FIELD_MISSING );
    $int = $array[$field];
    if( !is_int( $int ) )
        throw new Exception( __FUNCTION__ . ' failed to detect integer at `' . $field . '`', ErrCode::STRING_EXPECTED );
    return $int;
}
