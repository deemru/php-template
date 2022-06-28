<?php declare( strict_types = 1 );

namespace deemru;

require_once __DIR__ . '/common.php';

use Exception;

class Json
{
    /**
     * @var array<mixed, mixed>
     */
    private array $array;

    /**
    * Json constructor
    *
    * @param array<mixed, mixed> $array
    */
    public function __construct( array $array = [] )
    {
        $this->array = $array;
    }

    public function toString(): string
    {
        $string = json_encode( $this->array );
        if( $string === false )
            throw new Exception( __FUNCTION__ . ' failed to encode internal array `' . serialize( $this->array ) . '`', ErrCode::JSON_ENCODE );
        return $string;
    }

    /**
     * Gets Value by key
     *
     * @param mixed $key
     * @return Value
     */
    public function get( $key ): Value
    {
        if( !isset( $this->array[$key] ) )
            throw new Exception( __FUNCTION__ . ' failed to find key `' . $key . '`', ErrCode::KEY_MISSING );
        return new Value( $this->array[$key] );
    }

    /**
     * Puts value by key
     *
     * @param mixed $key
     * @param mixed $value
     * @return Json
     */
    public function put( $key, $value )
    {
        $this->array[$key] = $value;
    }

    /**
    * Gets a BlockHeaders value
    *
    * @return BlockHeaders
    */
    function asBlockHeaders(): BlockHeaders
    {
        return new BlockHeaders( $this );
    }

    /**
    * Gets a Balance value
    *
    * @return Balance
    */
    function asBalance(): Balance
    {
        return new Balance( $this );
    }

    /**
    * Gets an array of BlockHeaders value
    *
    * @return array<int, BlockHeaders>
    */
    function asArrayBlockHeaders(): array
    {
        $array = [];
        foreach( $this->array as $headers )
            $array[] = asValue( $headers )->asJson()->asBlockHeaders();
        return $array;
    }

    /**
    * Gets an array value
    *
    * @return array<int, Address>
    */
    function asArrayAddress(): array
    {
        $array = [];
        foreach( $this->array as $address )
            $array[] = Address::fromString( asValue( $address )->asString() );
        return $array;
    }

    /**
    * Gets an array value
    *
    * @return array<int, Balance>
    */
    function asArrayBalance(): array
    {
        $array = [];
        foreach( $this->array as $balance )
            $array[] = asValue( $balance )->asJson()->asBalance();
        return $array;
    }
}
