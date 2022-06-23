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
    * Gets an integer value
    *
    * @return int
    */
    function asInt(): int
    {
        if( !is_int( $this->value ) )
            throw new Exception( __FUNCTION__ . ' failed to detect integer at `' . $this->value . '`', ErrCode::INT_EXPECTED );
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
            throw new Exception( __FUNCTION__ . ' failed to detect integer at `' . $this->value . '`', ErrCode::STRING_EXPECTED );
        return $this->value;
    }

    /**
    * Gets a Json value
    *
    * @return Json
    */
    function asJson(): Json
    {
        if( !is_array( $this->value ) )
            throw new Exception( __FUNCTION__ . ' failed to detect Json at `' . $this->value . '`', ErrCode::ARRAY_EXPECTED );
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
            throw new Exception( __FUNCTION__ . ' failed to detect integer at `' . $this->value . '`', ErrCode::STRING_EXPECTED );
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
