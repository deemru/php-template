<?php declare( strict_types = 1 );

namespace deemru;

require_once __DIR__ . '/common.php';

use Exception;

class JsonTemplate
{
    private Json $json;

    public function __construct( Json $json )
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
        return $this->json->get( $key );
    }

    public function toString(): string { return $this->json->toString(); }
}
