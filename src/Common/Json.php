<?php declare( strict_types = 1 );

namespace wavesplatform\Common;

use Exception;
use wavesplatform\Common\ExceptionCode;

use wavesplatform\Transactions\Transaction;
use wavesplatform\Account\Address;
use wavesplatform\Model\Alias;
use wavesplatform\Model\BalanceDetails;
use wavesplatform\Model\DataEntry;
use wavesplatform\Model\ScriptInfo;
use wavesplatform\Model\BlockHeaders;
use wavesplatform\Model\Balance;
use wavesplatform\Model\AssetBalance;
use wavesplatform\Model\AssetDetails;
use wavesplatform\Model\AssetDistribution;
use wavesplatform\Model\BlockchainRewards;
use wavesplatform\Model\Block;
use wavesplatform\Model\ScriptDetails;
use wavesplatform\Model\ScriptMeta;
use wavesplatform\Model\LeaseInfo;
use wavesplatform\Model\TransactionInfo;
use wavesplatform\Model\TransactionWithStatus;
use wavesplatform\Model\TransactionStatus;
use wavesplatform\Model\Votes;

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
     * Gets Value by key or returns fallback value
     *
     * @param mixed $key
     * @param mixed $value
     * @return Value
     */
    function getOr( $key, $value ): Value
    {
        return $this->exists( $key ) ? $this->get( $key ) : Value::asValue( $value );
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
        return new BlockHeaders( $this->json );
    }

    /**
    * Gets a Balance value
    *
    * @return Balance
    */
    function asBalance(): Balance
    {
        return new Balance( $this->json );
    }

    /**
    * Gets a AssetBalance value
    *
    * @return AssetBalance
    */
    function asAssetBalance(): AssetBalance
    {
        return new AssetBalance( $this->json );
    }

    function asAssetDetails(): AssetDetails
    {
        return new AssetDetails( $this->json );
    }

    function asAssetDistribution(): AssetDistribution
    {
        return new AssetDistribution( $this->json );
    }

    /**
    * Gets a BalanceDetails value
    *
    * @return BalanceDetails
    */
    function asBalanceDetails(): BalanceDetails
    {
        return new BalanceDetails( $this->json );
    }

    function asBlockchainRewards(): BlockchainRewards
    {
        return new BlockchainRewards( $this->json );
    }

    function asBlock(): Block
    {
        return new Block( $this->json );
    }

    /**
    * Gets a DataEntry value
    *
    * @return DataEntry
    */
    function asDataEntry(): DataEntry
    {
        return new DataEntry( $this->json );
    }

    function asLeaseInfo(): LeaseInfo
    {
        return new LeaseInfo( $this->json );
    }

    function asScriptMeta(): ScriptMeta
    {
        return new ScriptMeta( $this->json );
    }

    function asScriptInfo(): ScriptInfo
    {
        return new ScriptInfo( $this->json );
    }

    function asScriptDetails(): ScriptDetails
    {
        return new ScriptDetails( $this->json );
    }

    function asTransactionInfo(): TransactionInfo
    {
        return new TransactionInfo( $this->json );
    }

    function asTransactionWithStatus(): TransactionWithStatus
    {
        return new TransactionWithStatus( $this->json );
    }

    function asTransactionStatus(): TransactionStatus
    {
        return new TransactionStatus( $this->json );
    }

    function asTransaction(): Transaction
    {
        return new Transaction( $this->json );
    }

    function asVotes(): Votes
    {
        return new Votes( $this->json );
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
    * Gets an array of Block value
    *
    * @return array<int, Block>
    */
    function asArrayBlock(): array
    {
        $array = [];
        foreach( $this->json as $headers )
            $array[] = Value::asValue( $headers )->asJson()->asBlock();
        return $array;
    }

    /**
    * Gets an array of LeaseInfo value
    *
    * @return array<int, LeaseInfo>
    */
    function asArrayLeaseInfo(): array
    {
        $array = [];
        foreach( $this->json as $info )
            $array[] = Value::asValue( $info )->asJson()->asLeaseInfo();
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
        foreach( $this->json as $assetBalance )
            $array[] = Value::asValue( $assetBalance )->asJson()->asAssetBalance();
        return $array;
    }

    /**
    * Gets an array value
    *
    * @return array<int, AssetDetails>
    */
    function asArrayAssetDetails(): array
    {
        $array = [];
        foreach( $this->json as $assetDetails )
            $array[] = Value::asValue( $assetDetails )->asJson()->asAssetDetails();
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

    /**
    * Gets an array value
    *
    * @return array<int, TransactionWithStatus>
    */
    function asArrayTransactionWithStatus(): array
    {
        $array = [];
        foreach( $this->json as $tx )
            $array[] = Value::asValue( $tx )->asJson()->asTransactionWithStatus();
        return $array;
    }

    /**
    * Gets an array value
    *
    * @return array<int, TransactionInfo>
    */
    function asArrayTransactionInfo(): array
    {
        $array = [];
        foreach( $this->json as $tx )
            $array[] = Value::asValue( $tx )->asJson()->asTransactionInfo();
        return $array;
    }

    /**
    * @return array<int, TransactionStatus>
    */
    function asArrayTransactionStatus(): array
    {
        $array = [];
        foreach( $this->json as $tx )
            $array[] = Value::asValue( $tx )->asJson()->asTransactionStatus();
        return $array;
    }

    /**
    * @return array<int, Transaction>
    */
    function asArrayTransaction(): array
    {
        $array = [];
        foreach( $this->json as $tx )
            $array[] = Value::asValue( $tx )->asJson()->asTransaction();
        return $array;
    }
}
