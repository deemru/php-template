<?php declare( strict_types = 1 );

namespace wavesplatform\Model;

use wavesplatform\Common\JsonTemplate;
use wavesplatform\Common\Value;

class ScriptMeta extends JsonTemplate
{
    function metaVersion(): int { return $this->get( 'version' )->asInt(); }
    /**
     * Gets a map of callable functions with their arguments as ArgMeta
     *
     * @return array<string, array<int, ArgMeta>>
     */
    function callableFunctions(): array
    {
        $map = [];
        $arrayFuncs = $this->get( 'callableFuncTypes' )->asArray();
        foreach( $arrayFuncs as $key => $value )
        {
            $function = Value::asValue( $key )->asString();
            $args = [];
            $arrayArgs = Value::asValue( $value )->asArray();
            foreach( $arrayArgs as $arg )
                $args[] = Value::asValue( $arg )->asArgMeta();
            $map[$function] = $args;
        }

        return $map;
    }
}
