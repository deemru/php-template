<?php declare( strict_types = 1 );

namespace deemru;

require_once __DIR__ . '/common.php';

class EntryType
{
    const BINARY = 1;
    const BOOLEAN = 2;
    const INTEGER = 3;
    const STRING = 4;
    const DELETE = 5;
}
