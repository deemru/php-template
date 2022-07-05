<?php declare( strict_types = 1 );

namespace wavesplatform\Model;

use Exception;
use wavesplatform\ExceptionCode;

class Json
{
    /**
     * @var array<mixed, mixed>
     */
    private array $array;

    /**
    * Json constructor
    *
    * @param array<mixed, mixed> $array
    */
    function __construct( array $array = [] )
    {
        $this->array = $array;
    }

    /**
    * Json function constructor
    *
    * @param array<mixed, mixed> $json
    * @return Json
    */
    static function asJson( array $json ): Json
    {
        return new Json( $json );
    }

    function toString(): string
    {
        $string = json_encode( $this->array );
        if( $string === false )
            throw new Exception( __FUNCTION__ . ' failed to encode internal array `' . serialize( $this->array ) . '`', ExceptionCode::JSON_ENCODE );
        return $string;
    }

    /**
     * Gets Value by key
     *
     * @param mixed $key
     * @return Value
     */
    function get( $key ): Value
    {
        if( !isset( $this->array[$key] ) )
            throw new Exception( __FUNCTION__ . ' failed to find key `' . $key . '`', ExceptionCode::KEY_MISSING );
        return new Value( $this->array[$key] );
    }

    /**
     * Checks key exists
     *
     * @param mixed $key
     * @return bool
     */
    function exists( $key ): bool
    {
        return isset( $this->array[$key] );
    }

    /**
     * Puts value by key
     *
     * @param mixed $key
     * @param mixed $value
     * @return Json
     */
    function put( $key, $value ): Json
    {
        $this->array[$key] = $value;
        return $this;
    }

    /**
    * Gets a BlockHeaders value
    *
    * @return BlockHeaders
    */
    function asBlockHeaders(): BlockHeaders
    {
        return new BlockHeaders( $this );
    }

    /**
    * Gets a Balance value
    *
    * @return Balance
    */
    function asBalance(): Balance
    {
        return new Balance( $this );
    }

    /**
    * Gets a BalanceDetails value
    *
    * @return BalanceDetails
    */
    function asBalanceDetails(): BalanceDetails
    {
        return new BalanceDetails( $this );
    }

    /**
    * Gets a DataEntry value
    *
    * @return DataEntry
    */
    function asDataEntry(): DataEntry
    {
        return new DataEntry( $this );
    }

    function asScriptMeta(): ScriptMeta
    {
        return new ScriptMeta( $this );
    }

    function asScriptInfo(): ScriptInfo
    {
        return new ScriptInfo( $this );
    }

    /**
    * Gets an array of BlockHeaders value
    *
    * @return array<int, BlockHeaders>
    */
    function asArrayBlockHeaders(): array
    {
        $array = [];
        foreach( $this->array as $headers )
            $array[] = Value::asValue( $headers )->asJson()->asBlockHeaders();
        return $array;
    }

    /**
    * Gets an array value
    *
    * @return array<int, Address>
    */
    function asArrayAddress(): array
    {
        $array = [];
        foreach( $this->array as $address )
            $array[] = Address::fromString( Value::asValue( $address )->asString() );
        return $array;
    }

    /**
    * Gets an array value
    *
    * @return array<int, Alias>
    */
    function asArrayAlias(): array
    {
        $array = [];
        foreach( $this->array as $alias )
            $array[] = Alias::fromFullAlias( Value::asValue( $alias )->asString() );
        return $array;
    }

    /**
    * Gets an array value
    *
    * @return array<int, Balance>
    */
    function asArrayBalance(): array
    {
        $array = [];
        foreach( $this->array as $balance )
            $array[] = Value::asValue( $balance )->asJson()->asBalance();
        return $array;
    }

    /**
    * Gets an array value
    *
    * @return array<int, DataEntry>
    */
    function asArrayDataEntry(): array
    {
        $array = [];
        foreach( $this->array as $data )
            $array[] = Value::asValue( $data )->asJson()->asDataEntry();
        return $array;
    }
}
