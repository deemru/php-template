<?php declare( strict_types = 1 );

namespace wavesplatform\Common;

use Exception;
use wavesplatform\Common\ExceptionCode;
use wavesplatform\Model\ArgMeta;
use wavesplatform\Model\Address;
use wavesplatform\Model\AssetId;
use wavesplatform\Model\Id;
use wavesplatform\Model\ChainId;
use wavesplatform\Model\ApplicationStatus;
use wavesplatform\Model\Status;

class Value
{
    /**
     * @var mixed
     */
    private $value;

    /**
     * @param mixed $value
     */
    function __construct( $value )
    {
        $this->value = $value;
    }

    /**
    * Value function constructor
    *
    * @param mixed $value
    * @return Value
    */
    static function asValue( $value ): Value
    {
        return new Value( $value );
    }

    /**
    * Gets an boolean value
    *
    * @return bool
    */
    function asBoolean(): bool
    {
        if( !is_bool( $this->value ) )
            throw new Exception( __FUNCTION__ . ' failed to detect boolean at `' . json_encode( $this->value ) . '`', ExceptionCode::BOOL_EXPECTED );
        return $this->value;
    }

    /**
    * Gets an integer value
    *
    * @return int
    */
    function asInt(): int
    {
        if( !is_int( $this->value ) )
        {
            if( is_string( $this->value ) )
            {
                $intval = intval( $this->value );
                if( strval( $intval ) === $this->value )
                    return $intval;
            }
            throw new Exception( __FUNCTION__ . ' failed to detect integer at `' . json_encode( $this->value ) . '`', ExceptionCode::INT_EXPECTED );
        }
        return $this->value;
    }

    /**
    * Gets a string value
    *
    * @return string
    */
    function asString(): string
    {
        if( !is_string( $this->value ) )
            throw new Exception( __FUNCTION__ . ' failed to detect string at `' . json_encode( $this->value ) . '`', ExceptionCode::STRING_EXPECTED );
        return $this->value;
    }

    function asChainId(): ChainId
    {
        if( is_int( $this->value ) )
            return ChainId::fromInt( $this->value );
        return ChainId::fromString( $this->asString() );
    }

    /**
    * Gets a base64 decoded string value
    *
    * @return string
    */
    function asBase64Decoded(): string
    {
        if( !is_string( $this->value ) )
            throw new Exception( __FUNCTION__ . ' failed to detect string at `' . json_encode( $this->value ) . '`', ExceptionCode::STRING_EXPECTED );
        if( substr( $this->value, 0, 7 ) !== 'base64:' )
            throw new Exception( __FUNCTION__ . ' failed to detect base64 `' . $this->value . '`', ExceptionCode::BASE64_DECODE );
        $decoded = base64_decode( substr( $this->value, 7 ) );
        if( !is_string( $decoded ) )
            throw new Exception( __FUNCTION__ . ' failed to decode base64 `' . substr( $this->value, 7 ) . '`', ExceptionCode::BASE64_DECODE );
        return $decoded;
    }

    /**
    * Gets a Json value
    *
    * @return Json
    */
    function asJson(): Json
    {
        if( !is_array( $this->value ) )
            throw new Exception( __FUNCTION__ . ' failed to detect Json at `' . json_encode( $this->value ) . '`', ExceptionCode::ARRAY_EXPECTED );
        return Json::asJson( $this->value );
    }

    /**
    * Gets an array value
    *
    * @return array<mixed, mixed>
    */
    function asArray(): array
    {
        if( !is_array( $this->value ) )
            throw new Exception( __FUNCTION__ . ' failed to detect array at `' . json_encode( $this->value ) . '`', ExceptionCode::ARRAY_EXPECTED );
        return $this->value;
    }

    /**
    * Gets an array of integers value
    *
    * @return array<int, int>
    */
    function asArrayInt(): array
    {
        if( !is_array( $this->value ) )
            throw new Exception( __FUNCTION__ . ' failed to detect array at `' . json_encode( $this->value ) . '`', ExceptionCode::ARRAY_EXPECTED );
        $ints = [];
        foreach( $this->value as $value )
            $ints[] = Value::asValue( $value )->asInt();
        return $ints;
    }

    /**
    * Gets an array of string to integer map
    *
    * @return array<string, int>
    */
    function asMapStringInt(): array
    {
        if( !is_array( $this->value ) )
            throw new Exception( __FUNCTION__ . ' failed to detect array at `' . json_encode( $this->value ) . '`', ExceptionCode::ARRAY_EXPECTED );
        $ints = [];
        foreach( $this->value as $key => $value )
            $ints[Value::asValue( $key )->asString()] = Value::asValue( $value )->asInt();
        return $ints;
    }

    function asArgMeta(): ArgMeta
    {
        return new ArgMeta( $this->asJson() );
    }

    /**
    * Gets an Address value
    *
    * @return Address
    */
    function asAddress(): Address
    {
        return Address::fromString( $this->asString() );
    }

    /**
    * Gets an AssetId value
    *
    * @return AssetId
    */
    function asAssetId(): AssetId
    {
        return AssetId::fromString( $this->asString() );
    }

    /**
    * Gets an Id value
    *
    * @return Id
    */
    function asId(): Id
    {
        return Id::fromString( $this->asString() );
    }

    function asApplicationStatus(): int
    {
        switch( $this->asString() )
        {
            case ApplicationStatus::SUCCEEDED_S: return ApplicationStatus::SUCCEEDED;
            case ApplicationStatus::SCRIPT_EXECUTION_FAILED_S: return ApplicationStatus::SCRIPT_EXECUTION_FAILED;
            default: return ApplicationStatus::UNKNOWN;
        }
    }

    function asStatus(): int
    {
        switch( $this->asString() )
        {
            case Status::CONFIRMED_S: return Status::CONFIRMED;
            case Status::UNCONFIRMED_S: return Status::UNCONFIRMED;
            case Status::NOT_FOUND_S: return Status::NOT_FOUND;
            default: return Status::UNKNOWN;
        }
    }
}
