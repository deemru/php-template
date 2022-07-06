<?php declare( strict_types = 1 );

namespace wavesplatform\Model;

use Exception;
use wavesplatform\ExceptionCode;

class Json
{
    /**
     * @var array<mixed, mixed>
     */
    private array $json;

    /**
    * Json constructor
    *
    * @param array<mixed, mixed> $json
    */
    function __construct( array $json = [] )
    {
        $this->json = $json;
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
        $string = json_encode( $this->json );
        if( $string === false )
            throw new Exception( __FUNCTION__ . ' failed to encode internal array `' . serialize( $this->json ) . '`', ExceptionCode::JSON_ENCODE );
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
        if( !isset( $this->json[$key] ) )
            throw new Exception( __FUNCTION__ . ' failed to find key `' . $key . '`', ExceptionCode::KEY_MISSING );
        return new Value( $this->json[$key] );
    }

    /**
     * Checks key exists
     *
     * @param mixed $key
     * @return bool
     */
    function exists( $key ): bool
    {
        return isset( $this->json[$key] );
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
        $this->json[$key] = $value;
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
    * Gets a AssetBalance value
    *
    * @return AssetBalance
    */
    function asAssetBalance(): AssetBalance
    {
        return new AssetBalance( $this );
    }

    function asAssetDetails(): AssetDetails
    {
        return new AssetDetails( $this );
    }

    function asAssetDistribution(): AssetDistribution
    {
        return new AssetDistribution( $this );
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

    function asScriptDetails(): ScriptDetails
    {
        return new ScriptDetails( $this );
    }

    /**
    * Gets an array of BlockHeaders value
    *
    * @return array<int, BlockHeaders>
    */
    function asArrayBlockHeaders(): array
    {
        $array = [];
        foreach( $this->json as $headers )
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
        foreach( $this->json as $address )
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
        foreach( $this->json as $alias )
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
        foreach( $this->json as $balance )
            $array[] = Value::asValue( $balance )->asJson()->asBalance();
        return $array;
    }

    /**
    * Gets an array value
    *
    * @return array<int, AssetBalance>
    */
    function asArrayAssetBalance(): array
    {
        $array = [];
        foreach( $this->json as $balance )
            $array[] = Value::asValue( $balance )->asJson()->asAssetBalance();
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
        foreach( $this->json as $data )
            $array[] = Value::asValue( $data )->asJson()->asDataEntry();
        return $array;
    }
}
