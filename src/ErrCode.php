<?php declare( strict_types = 1 );

namespace deemru;

require_once __DIR__ . '/common.php';

class ErrCode
{
    const BASE = 9700537 << 8;
    const BASE58_DECODE =   ErrCode::BASE | 1;
    const BASE58_ENCODE =   ErrCode::BASE | 2;
    const FETCH_URI =       ErrCode::BASE | 3;
    const JSON_DECODE =     ErrCode::BASE | 4;
    const STRING_EXPECTED = ErrCode::BASE | 5;
    const FIELD_MISSING =   ErrCode::BASE | 6;
}
