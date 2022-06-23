<?php declare( strict_types = 1 );

namespace deemru;

require_once __DIR__ . '/common.php';

class ErrCode
{
    const BASE = 970053700;
    const BASE58_DECODE =   ErrCode::BASE | 1;
    const BASE58_ENCODE =   ErrCode::BASE | 2;
    const FETCH_URI =       ErrCode::BASE | 3;
    const JSON_ENCODE =     ErrCode::BASE | 4;
    const JSON_DECODE =     ErrCode::BASE | 5;
    const KEY_MISSING =     ErrCode::BASE | 6;
    const STRING_EXPECTED = ErrCode::BASE | 7;
    const INT_EXPECTED =    ErrCode::BASE | 8;
    const ARRAY_EXPECTED =  ErrCode::BASE | 9;
}
