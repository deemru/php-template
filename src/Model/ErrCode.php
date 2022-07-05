<?php declare( strict_types = 1 );

namespace wavesplatform\Model;

class ExceptionCode
{
    const BASE = 970053700;
    const BASE58_DECODE =   ExceptionCode::BASE | 1;
    const BASE58_ENCODE =   ExceptionCode::BASE | 2;
    const FETCH_URI =       ExceptionCode::BASE | 3;
    const JSON_ENCODE =     ExceptionCode::BASE | 4;
    const JSON_DECODE =     ExceptionCode::BASE | 5;
    const KEY_MISSING =     ExceptionCode::BASE | 6;
    const STRING_EXPECTED = ExceptionCode::BASE | 7;
    const INT_EXPECTED =    ExceptionCode::BASE | 8;
    const ARRAY_EXPECTED =  ExceptionCode::BASE | 9;
    const UNKNOWN_TYPE =    ExceptionCode::BASE | 10;
    const BOOL_EXPECTED =   ExceptionCode::BASE | 11;
    const BASE64_DECODE =   ExceptionCode::BASE | 12;
}
