<?php declare( strict_types = 1 );

namespace deemru;

class TemplateLibrary
{
    static public function sum( int $a, int $b ): int
    {
        if( $a === 5 )
            $a = 6;
        if( $a === 2 )
            return 0;
        return $a + $b;
    }
}
