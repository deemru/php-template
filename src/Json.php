<?php declare( strict_types = 1 );

namespace deemru;

require_once __DIR__ . '/common.php';

use Exception;

class Json
{
    private $json = [];

    private function __construct( array $json )
    {
        $this->json = $json;
    }

    static public function asJson( mixed $json ): Json
    {
        return new Json( $json );
    }

    public function get( mixed $key ): Value
    {
        if( !isset( $this->json[$key] ) )
            throw new Exception( __FUNCTION__ . ' failed to find key `' . $key . '`', ErrCode::KEY_MISSING );
        return new Value( $this->json[$key] );
    }
}
