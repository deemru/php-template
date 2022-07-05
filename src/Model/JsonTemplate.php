<?php declare( strict_types = 1 );

namespace wavesplatform\Model;

use Exception;
use wavesplatform\ExceptionCode;

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

    /**
     * Checks key exists
     *
     * @param mixed $key
     * @return bool
     */
    public function exists( $key ): bool
    {
        return $this->json->exists( $key );
    }

    public function toString(): string { return $this->json->toString(); }
}
