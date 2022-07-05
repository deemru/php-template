<?php declare( strict_types = 1 );

namespace wavesplatform\Model;

use Exception;
use wavesplatform\ExceptionCode;

class Alias
{
    const PREFIX = 'alias:';
    const TYPE = 2;
    const MIN_LENGTH = 4;
    const MAX_LENGTH = 30;
    const BYTES_LENGTH = 1 + 1 + Alias::MAX_LENGTH;

    const ALPHABET = '-.0-9@_a-z';
    const MATCH = '/[' . Alias::ALPHABET . ']{' . Alias::MIN_LENGTH . ',' . Alias::MAX_LENGTH . '}/';

    private string $bytes;
    private string $name;
    private string $fullAlias;

    function __construct( string $alias, string $chainId = '' )
    {
        $matches = [];
        preg_match( Alias::MATCH, $alias, $matches );
        if( !isset( $matches[0] ) || $matches[0] !== $alias )
            throw new Exception( __FUNCTION__ . ' bad alias name = `' . serialize( $alias ) . '`', ExceptionCode::BAD_ALIAS );
        if( $chainId === '' || strlen( $chainId ) !== 1 )
            $chainId = WavesConfig::chainId();
        $this->name = $alias;
        $this->bytes = Alias::TYPE . $chainId . $alias;
        $this->fullAlias = Alias::PREFIX . $chainId . ':' . $alias;
    }

    static function fromFullAlias( string $fullAlias ): Alias
    {
        if( strlen( $fullAlias ) >= 12 )
        {
            $prefix = substr( $fullAlias, 0, strlen( Alias::PREFIX ) );
            if( $prefix === Alias::PREFIX && $fullAlias[7] === ':' )
            {
                $chainId = $fullAlias[6];
                $alias = substr( $fullAlias, 8 );
                return new Alias( $alias, $chainId );
            }
        }

        throw new Exception( __FUNCTION__ . ' bad alias name = `' . serialize( $fullAlias ) . '`', ExceptionCode::BAD_ALIAS );
    }

    static function isValid( string $alias, string $chainId = '' ): bool
    {
        return $alias === (new Alias( $alias, $chainId ))->name();
    }

    function type(): int
    {
        return Alias::TYPE;
    }

    function chainId(): string
    {
        return $this->bytes[1];
    }

    function name(): string
    {
        return $this->name;
    }

    function bytes(): string
    {
        return $this->bytes;
    }

    function toString(): string
    {
        return $this->fullAlias;
    }
}
