<?php declare( strict_types = 1 );

namespace wavesplatform\Model;

use Exception;
use wavesplatform\Common\ExceptionCode;
use wavesplatform\Common\JsonBase;

class DataEntry extends JsonBase
{
    function key(): string { return $this->json->get( 'key' )->asString(); }

    function type(): int
    {
        if( !$this->json->exists( 'type' ) )
            return EntryType::DELETE;
        switch( $this->json->get( 'type' )->asString() )
        {
            case 'binary': return EntryType::BINARY;
            case 'boolean': return EntryType::BOOLEAN;
            case 'integer': return EntryType::INTEGER;
            case 'string': return EntryType::STRING;
            default: throw new Exception( __FUNCTION__ . ' failed to detect type `' . serialize( $this->json->get( 'type' ) ) . '`', ExceptionCode::UNKNOWN_TYPE );
        }
    }

    /**
     * Returns value of native type
     *
     * @return bool|int|string|null
     */
    function value()
    {
        switch( $this->type() )
        {
            case EntryType::BINARY: return $this->json->get( 'value' )->asBase64Decoded();
            case EntryType::BOOLEAN: return $this->json->get( 'value' )->asBoolean();
            case EntryType::INTEGER: return $this->json->get( 'value' )->asInt();
            case EntryType::STRING: return $this->json->get( 'value' )->asString();
            case EntryType::DELETE: return null;
        }

        throw new Exception( __FUNCTION__ . ' failed to detect type `' . serialize( $this->json->get( 'type' ) ) . '`', ExceptionCode::UNKNOWN_TYPE ); // @codeCoverageIgnore
    }
}
