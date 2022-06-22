<?php declare( strict_types = 1 );

namespace deemru;

require_once __DIR__ . '/common.php';

use Exception;

class Json
{
    /**
     * @var array<int|string, mixed>
     */
    private array $json = [];

    /**
    * Json constructor
    *
    * @param array<int|string, mixed> $json
    */
    private function __construct( array $json )
    {
        $this->json = $json;
    }

    /**
    * Json static constructor
    *
    * @param array<int|string, mixed> $json
    */
    static public function asJson( array $json ): Json
    {
        return new Json( $json );
    }

    /**
     * Gets Value by key
     *
     * @param int|string $key
     * @return Value
     */
    public function get( int|string $key ): Value
    {
        if( !isset( $this->json[$key] ) )
            throw new Exception( __FUNCTION__ . ' failed to find key `' . $key . '`', ErrCode::KEY_MISSING );
        return new Value( $this->json[$key] );
    }
}
