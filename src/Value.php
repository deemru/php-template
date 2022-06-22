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
}
