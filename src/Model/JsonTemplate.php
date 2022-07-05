<?php declare( strict_types = 1 );

namespace wavesplatform\Model;

class JsonTemplate
{
    private Json $json;

    function __construct( Json $json )
    {
        $this->json = $json;
    }

    /**
     * Gets Value by key
     *
     * @param mixed $key
     * @return Value
     */
    function get( $key ): Value
    {
        return $this->json->get( $key );
    }

    /**
     * Checks key exists
     *
     * @param mixed $key
     * @return bool
     */
    function exists( $key ): bool
    {
        return $this->json->exists( $key );
    }

    function toString(): string { return $this->json->toString(); }
}
