<?php declare( strict_types = 1 );

namespace deemru;

require_once __DIR__ . '/common.php';

use Exception;

class Json
{
    /**
     * @var array<mixed, mixed>
     */
    private array $json = [];

    /**
    * Json constructor
    *
    * @param array<mixed, mixed> $json
    */
    public function __construct( array $json )
    {
        $this->json = $json;
    }

    /**
     * Gets Value by key
     *
     * @param mixed $key
     * @return Value
     */
    public function get( $key ): Value
    {
        if( !isset( $this->json[$key] ) )
            throw new Exception( __FUNCTION__ . ' failed to find key `' . $key . '`', ErrCode::KEY_MISSING );
        return new Value( $this->json[$key] );
    }

    /**
    * Gets an BlockHeaders value
    *
    * @return BlockHeaders
    */
    function asBlockHeaders(): BlockHeaders
    {
        return new BlockHeaders( $this );
    }

    /**
    * Gets an array value
    *
    * @return array<int, Address>
    */
    function asArrayAddress(): array
    {
        $addresses = [];
        foreach( $this->json as $address )
            $addresses[] = Address::fromString( asValue( $address )->asString() );
        return $addresses;
    }
}
