<?php declare( strict_types = 1 );

namespace deemru;

require_once __DIR__ . '/common.php';

use Exception;

class Value
{
    /**
     * @var mixed
     */
    private $value;

    /**
     * @param mixed $value
     */
    public function __construct( $value )
    {
        $this->value = $value;
    }

    /**
    * Gets an boolean value
    *
    * @return bool
    */
    function asBoolean(): bool
    {
        if( !is_bool( $this->value ) )
            throw new Exception( __FUNCTION__ . ' failed to detect boolean at `' . json_encode( $this->value ) . '`', ErrCode::BOOL_EXPECTED );
        return $this->value;
    }

    /**
    * Gets an integer value
    *
    * @return int
    */
    function asInt(): int
    {
        if( !is_int( $this->value ) )
            throw new Exception( __FUNCTION__ . ' failed to detect integer at `' . json_encode( $this->value ) . '`', ErrCode::INT_EXPECTED );
        return $this->value;
    }

    /**
    * Gets a string value
    *
    * @return string
    */
    function asString(): string
    {
        if( !is_string( $this->value ) )
            throw new Exception( __FUNCTION__ . ' failed to detect string at `' . json_encode( $this->value ) . '`', ErrCode::STRING_EXPECTED );
        return $this->value;
    }

    /**
    * Gets a base64 decoded string value
    *
    * @return string
    */
    function asBase64Decoded(): string
    {
        if( !is_string( $this->value ) )
            throw new Exception( __FUNCTION__ . ' failed to detect string at `' . json_encode( $this->value ) . '`', ErrCode::STRING_EXPECTED );
        if( substr( $this->value, 0, 7 ) !== 'base64:' )
            throw new Exception( __FUNCTION__ . ' failed to detect base64 `' . $this->value . '`', ErrCode::BASE64_DECODE );
        $decoded = base64_decode( substr( $this->value, 7 ) );
        if( $decoded === false )
            throw new Exception( __FUNCTION__ . ' failed to decode base64 `' . substr( $this->value, 7 ) . '`', ErrCode::BASE64_DECODE );
        return $decoded;
    }

    /**
    * Gets a Json value
    *
    * @return Json
    */
    function asJson(): Json
    {
        if( !is_array( $this->value ) )
            throw new Exception( __FUNCTION__ . ' failed to detect Json at `' . json_encode( $this->value ) . '`', ErrCode::ARRAY_EXPECTED );
        return asJson( $this->value );
    }

    /**
    * Gets an array of integers value
    *
    * @return array<int>
    */
    function asArrayInt(): array
    {
        if( !is_array( $this->value ) )
            throw new Exception( __FUNCTION__ . ' failed to detect array at `' . json_encode( $this->value ) . '`', ErrCode::ARRAY_EXPECTED );
        $ints = [];
        foreach( $this->value as $value )
            $ints[] = asValue( $value )->asInt();
        return $ints;
    }

    /**
    * Gets an Address value
    *
    * @return Address
    */
    function asAddress(): Address
    {
        return Address::fromString( $this->asString() );
    }
}
