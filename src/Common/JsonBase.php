<?php declare( strict_types = 1 );

namespace wavesplatform\Common;

class JsonBase
{
    protected Json $json;

    function __construct( Json $json )
    {
        $this->json = $json;
    }

    function toString(): string
    {
        return $this->json->toString();
    }

    function json(): Json
    {
        return $this->json;
    }
}
