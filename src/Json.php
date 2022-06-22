<?php declare( strict_types = 1 );

namespace deemru;

require_once __DIR__ . '/common.php';

use Exception;

class Json
{
    private array $json = [];

    /**
    * Json constructor
    *
    * @param array<mixed, mixed> $json
    */
    private function __construct( array $json )
    {
        $this->json = $json;
    }

    /**
    * Json static constructor
    *
    * @param array<mixed, mixed> $json
    */
    static public function asJson( mixed $json ): Json
    {
        return new Json( $json );
    }

    /**
     * Gets Value by key
     *
     * @param string|int $key
     * @return Value
     */
    public function get( mixed $key ): Value
    {
        if( !isset( $this->json[$key] ) )
            throw new Exception( __FUNCTION__ . ' failed to find key `' . $key . '`', ErrCode::KEY_MISSING );
        return new Value( $this->json[$key] );
    }
}
