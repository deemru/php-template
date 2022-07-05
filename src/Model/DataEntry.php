<?php declare( strict_types = 1 );

namespace wavesplatform\Model;

use Exception;
use wavesplatform\ExceptionCode;

class DataEntry extends JsonTemplate
{
    public function key(): string { return $this->get( 'key' )->asString(); }

    public function type(): int
    {
        if( !$this->exists( 'type' ) )
            return EntryType::DELETE;
        switch( $this->get( 'type' )->asString() )
        {
            case 'binary': return EntryType::BINARY;
            case 'boolean': return EntryType::BOOLEAN;
            case 'integer': return EntryType::INTEGER;
            case 'string': return EntryType::STRING;
            default: throw new Exception( __FUNCTION__ . ' failed to detect type `' . serialize( $this->get( 'type' ) ) . '`', ExceptionCode::UNKNOWN_TYPE );
        }
    }

    /**
     * Returns value of native type
     *
     * @return bool|int|string|null
     */
    public function value()
    {
        switch( $this->type() )
        {
            case EntryType::BINARY: return $this->get( 'value' )->asBase64Decoded();
            case EntryType::BOOLEAN: return $this->get( 'value' )->asBoolean();
            case EntryType::INTEGER: return $this->get( 'value' )->asInt();
            case EntryType::STRING: return $this->get( 'value' )->asString();
            case EntryType::DELETE: return null;
        }

        throw new Exception( __FUNCTION__ . ' failed to detect type `' . serialize( $this->get( 'type' ) ) . '`', ExceptionCode::UNKNOWN_TYPE ); // @codeCoverageIgnore
    }
}
