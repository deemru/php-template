<?php declare( strict_types = 1 );

namespace wavesplatform\API;

use Exception;
use wavesplatform\Common\ExceptionCode;

use deemru\WavesKit;

use wavesplatform\Common\Json;
use wavesplatform\Model\Address;
use wavesplatform\Model\AssetId;
use wavesplatform\Model\AssetDistribution;
use wavesplatform\Model\AssetBalance;
use wavesplatform\Model\AssetDetails;
use wavesplatform\Model\Alias;
use wavesplatform\Model\Balance;
use wavesplatform\Model\BalanceDetails;
use wavesplatform\Model\Block;
use wavesplatform\Model\Id;
use wavesplatform\Model\LeaseInfo;
use wavesplatform\Model\BlockHeaders;
use wavesplatform\Model\BlockchainRewards;
use wavesplatform\Model\ChainId;
use wavesplatform\Model\DataEntry;
use wavesplatform\Model\ScriptInfo;
use wavesplatform\Model\ScriptMeta;
use wavesplatform\Model\TransactionInfo;
use wavesplatform\Model\TransactionStatus;
use wavesplatform\Transactions\Transaction;

class Node
{
    const MAINNET = "https://nodes.wavesnodes.com";
    const TESTNET = "https://nodes-testnet.wavesnodes.com";
    const STAGENET = "https://nodes-stagenet.wavesnodes.com";
    const LOCAL = "http://127.0.0.1:6869";

    private WavesKit $wk;
    private string $chainId;
    private string $uri;

    private string $wklevel = '';
    private string $wkmessage = '';

    /**
     * Creates Node instance
     *
     * @param string $uri Node REST API address
     * @param string $chainId Chain ID or "?" to set automatically (Default: "")
     */
    function __construct( string $uri, string $chainId = '' )
    {
        $this->uri = $uri;
        $this->wk = new WavesKit( '?', function( string $wklevel, string $wkmessage )
        {
            $this->wklevel = $wklevel;
            $this->wkmessage = $wkmessage;
        } );
        $this->wk->setNodeAddress( $uri, 0 );

        if( $chainId === '?' )
            $this->chainId = $this->getAddresses()[0]->chainId();
        else
        if( strlen( $chainId ) === 1 )
            $this->chainId = $chainId;
        else
        if( $uri === Node::MAINNET )
            $this->chainId = ChainId::MAINNET;
        else
        if( $uri === Node::TESTNET )
            $this->chainId = ChainId::TESTNET;
        else
        if( $uri === Node::STAGENET )
            $this->chainId = ChainId::STAGENET;
        else
            $this->chainId = $this->getAddresses()[0]->chainId();

        $this->wk->chainId = $this->chainId; // @phpstan-ignore-line // accept workaround
    }

    function chainId(): string
    {
        return $this->chainId;
    }

    function uri(): string
    {
        return $this->uri;
    }

    /**
     * Fetches a custom REST API request
     *
     * @param string $uri
     * @param Json|string|null $data
     * @return Json
     */
    private function fetch( string $uri, $data = null )
    {
        if( isset( $data ) )
        {
            if( is_string( $data ) )
                $fetch = $this->wk->fetch( $uri, true, $data, null, [ 'Content-Type: text/plain', 'Accept: application/json' ] );
            else
                $fetch = $this->wk->fetch( $uri, true, $data->toString() );
        }
        else
            $fetch = $this->wk->fetch( $uri );
        if( $fetch === false )
        {
            $message = __FUNCTION__ . ' failed at `' . $uri . '`';
            if( $this->wklevel === 'e' )
                $message .= ' (WavesKit: ' . $this->wkmessage . ')';
            throw new Exception( $message, ExceptionCode::FETCH_URI );
        }
        $fetch = $this->wk->json_decode( $fetch );
        if( $fetch === false )
            throw new Exception( __FUNCTION__ . ' failed to decode `' . $uri . '`', ExceptionCode::JSON_DECODE );
        return Json::asJson( $fetch );
    }

    /**
     * GETs a custom REST API request
     *
     * @param string $uri
     * @return Json
     */
    function get( string $uri ): Json
    {
        return $this->fetch( $uri );
    }

    /**
     * POSTs a custom REST API request
     *
     * @param string $uri
     * @param Json|string $data
     * @return Json
     */
    function post( string $uri, $data ): Json
    {
        return $this->fetch( $uri, $data );
    }

    //===============
    // ADDRESSES
    //===============

    /**
     * Return addresses of the node
     *
     * @return array<int, Address>
     */
    function getAddresses(): array
    {
        return $this->get( '/addresses' )->asArrayAddress();
    }

    /**
     * Return addresses of the node by indexes
     *
     * @return array<int, Address>
     */
    function getAddressesByIndexes( int $fromIndex, int $toIndex ): array
    {
        return $this->get( '/addresses/seq/' . $fromIndex . '/' . $toIndex )->asArrayAddress();
    }

    function getBalance( Address $address, int $confirmations = null ): int
    {
        $uri = '/addresses/balance/' . $address->toString();
        if( isset( $confirmations ) )
            $uri .= '/' . $confirmations;
        return $this->get( $uri )->get( 'balance' )->asInt();
    }

    /**
     * Gets addresses balances
     *
     * @param array<int, Address> $addresses
     * @param int|null $height (default: null)
     * @return array<int, Balance>
     */
    function getBalances( array $addresses, int $height = null ): array
    {
        $json = new Json;

        $array = [];
        foreach( $addresses as $address )
            $array[] = $address->toString();
        $json->put( 'addresses', $array );

        if( isset( $height ) )
            $json->put( 'height', $height );

        return $this->post( '/addresses/balance', $json )->asArrayBalance();
    }

    function getBalanceDetails( Address $address ): BalanceDetails
    {
        return $this->get( '/addresses/balance/details/' . $address->toString() )->asBalanceDetails();
    }

    /**
     * Gets DataEntry array of address
     *
     * @param Address $address
     * @param string|null $regex (default: null)
     * @return array<int, DataEntry>
     */
    function getData( Address $address, string $regex = null ): array
    {
        $uri = '/addresses/data/' . $address->toString();
        if( isset( $regex ) )
            $uri .= '?matches=' . urlencode( $regex );
        return $this->get( $uri )->asArrayDataEntry();
    }

    /**
     * Gets DataEntry array of address by keys
     *
     * @param Address $address
     * @param array<int, string> $keys
     * @return array<int, DataEntry>
     */
    function getDataByKeys( Address $address, array $keys ): array
    {
        $json = new Json;

        $array = [];
        foreach( $keys as $key )
            $array[] = $key;
        $json->put( 'keys', $array );

        return $this->post( '/addresses/data/' . $address->toString(), $json )->asArrayDataEntry();
    }

    /**
     * Gets a single DataEntry of address by a key
     *
     * @param Address $address
     * @param string $key
     * @return DataEntry
     */
    function getDataByKey( Address $address, string $key ): DataEntry
    {
        return $this->get( '/addresses/data/' . $address->toString() . '/' . $key )->asDataEntry();
    }

    function getScriptInfo( Address $address ): ScriptInfo
    {
        return $this->get( '/addresses/scriptInfo/' . $address->toString() )->asScriptInfo();
    }

    function getScriptMeta( Address $address ): ScriptMeta
    {
        $json = $this->get( '/addresses/scriptInfo/' . $address->toString() . '/meta' );
        if( !$json->exists( 'meta' ) )
            $json->put( 'meta', [ 'version' => 0, 'callableFuncTypes' => [] ] );
        return $json->get( 'meta' )->asJson()->asScriptMeta();
    }

    //===============
    // ALIAS
    //===============

    /**
     * Gets an array of aliases by address
     *
     * @param Address $address
     * @return array<int, Alias>
     */
    function getAliasesByAddress( Address $address ): array
    {
        return $this->get( '/alias/by-address/' . $address->toString() )->asArrayAlias();
    }

    function getAddressByAlias( Alias $alias ): Address
    {
        return $this->get( '/alias/by-alias/' . $alias->name() )->get( 'address' )->asAddress();
    }

    //===============
    // ASSETS
    //===============

    function getAssetDistribution( AssetId $assetId, int $height, int $limit = 1000, string $after = null ): AssetDistribution
    {
        $uri = '/assets/' . $assetId->toString() . '/distribution/' . $height . '/limit/' . $limit;
        if( isset( $after ) )
            $uri .= '?after=' . $after;
        return $this->get( $uri )->asAssetDistribution();
    }

    /**
     * Gets an array of AssetBalance for an address
     *
     * @param Address $address
     * @return array<int, AssetBalance>
     */
    function getAssetsBalance( Address $address ): array
    {
        return $this->get( '/assets/balance/' . $address->toString() )->get( 'balances' )->asJson()->asArrayAssetBalance();
    }

    function getAssetBalance( Address $address, AssetId $assetId ): int
    {
        return $assetId->isWaves() ?
            $this->getBalance( $address ) :
            $this->get( '/assets/balance/' . $address->toString() . '/' . $assetId->toString() )->get( 'balance' )->asInt();
    }

    function getAssetDetails( AssetId $assetId ): AssetDetails
    {
        return $this->get( '/assets/details/' . $assetId->toString() . '?full=true' )->asAssetDetails();
    }

    /**
     * @param array<int, AssetId> $assetIds
     * @return array<int, AssetDetails>
     */
    function getAssetsDetails( array $assetIds ): array
    {
        $json = new Json;

        $array = [];
        foreach( $assetIds as $assetId )
            $array[] = $assetId->toString();
        $json->put( 'ids', $array );

        return $this->post( '/assets/details?full=true', $json )->asArrayAssetDetails();
    }

    /**
     * @return array<int, AssetDetails>
     */
    function getNft( Address $address, int $limit = 1000, AssetId $after = null ): array
    {
        $uri = '/assets/nft/' . $address->toString() . '/limit/' . $limit;
        if( isset( $after ) )
            $uri .= '?after=' . $after->toString();
        return $this->get( $uri )->asArrayAssetDetails();
    }

    //===============
    // BLOCKCHAIN
    //===============

    function getBlockchainRewards( int $height = null ): BlockchainRewards
    {
        $uri = '/blockchain/rewards';
        if( isset( $height ) )
            $uri .= '/' . $height;
        return $this->get( $uri )->asBlockchainRewards();
    }

    //===============
    // BLOCKS
    //===============

    function getHeight(): int
    {
        return $this->get( '/blocks/height' )->get( 'height' )->asInt();
    }

    function getBlockHeightById( string $blockId ): int
    {
        return $this->get( '/blocks/height/' . $blockId )->get( 'height' )->asInt();
    }

    function getBlockHeightByTimestamp( int $timestamp ): int
    {
        return $this->get( "/blocks/heightByTimestamp/" . $timestamp )->get( "height" )->asInt();
    }

    function getBlocksDelay( string $startBlockId, int $blocksNum ): int
    {
        return $this->get( "/blocks/delay/" . $startBlockId . "/" . $blocksNum )->get( "delay" )->asInt();
    }

    function getBlockHeadersByHeight( int $height ): BlockHeaders
    {
        return $this->get( "/blocks/headers/at/" . $height )->asBlockHeaders();
    }

    function getBlockHeadersById( string $blockId ): BlockHeaders
    {
        return $this->get( "/blocks/headers/" . $blockId )->asBlockHeaders();
    }

    /**
     * Get an array of BlockHeaders from fromHeight to toHeight
     *
     * @param integer $fromHeight
     * @param integer $toHeight
     * @return array<int, BlockHeaders>
     */
    function getBlocksHeaders( int $fromHeight, int $toHeight ): array
    {
        return $this->get( "/blocks/headers/seq/" . $fromHeight . "/" . $toHeight )->asArrayBlockHeaders();
    }

    function getLastBlockHeaders(): BlockHeaders
    {
        return $this->get( "/blocks/headers/last" )->asBlockHeaders();
    }

    function getBlockByHeight( int $height ): Block
    {
        return $this->get( '/blocks/at/' . $height )->asBlock();
    }

    function getBlockById( Id $id ): Block
    {
        return $this->get( '/blocks/' . $id->toString() )->asBlock();
    }

    /**
     * @return array<int, Block>
     */
    function getBlocks( int $fromHeight, int $toHeight ): array
    {
        return $this->get( '/blocks/seq/' . $fromHeight . '/' . $toHeight )->asArrayBlock();
    }

    function getGenesisBlock(): Block
    {
        return $this->get( '/blocks/first' )->asBlock();
    }

    function getLastBlock(): Block
    {
        return $this->get( '/blocks/last' )->asBlock();
    }

    /**
     * @return array<int, Block>
     */
    function getBlocksGeneratedBy( Address $generator, int $fromHeight, int $toHeight ): array
    {
        return $this->get( '/blocks/address/' . $generator->toString() . '/' . $fromHeight . '/' . $toHeight )->asArrayBlock();
    }

    //===============
    // NODE
    //===============

    function getVersion(): string
    {
        return $this->get( '/node/version')->get( 'version' )->asString();
    }

    //===============
    // DEBUG
    //===============
/*
    public List<HistoryBalance> getBalanceHistory(Address address) throws IOException, NodeException {
        return asType(get("/debug/balances/history/" + address.toString()), TypeRef.HISTORY_BALANCES);
    }

    public <T extends Transaction> Validation validateTransaction(T transaction) throws IOException, NodeException {
        return asType(post("/debug/validate")
                .setEntity(new StringEntity(transaction.toJson(), ContentType.APPLICATION_JSON)), TypeRef.VALIDATION);
    }
*/
    //===============
    // LEASING
    //===============

    /**
     * @return array<int, LeaseInfo>
     */
    function getActiveLeases( Address $address ): array
    {
        return $this->get( '/leasing/active/' . $address->toString() )->asArrayLeaseInfo();
    }

    function getLeaseInfo( Id $leaseId ): LeaseInfo
    {
        return $this->get( '/leasing/info/' . $leaseId->toString() )->asLeaseInfo();
    }

    /**
     * @param array<int, Id> $leaseIds
     * @return array<int, LeaseInfo>
     */
    function getLeasesInfo( array $leaseIds ): array
    {
        $json = new Json;

        $array = [];
        foreach( $leaseIds as $leaseId )
            $array[] = $leaseId->toString();
        $json->put( 'ids', $array );

        return $this->post( '/leasing/info', $json )->asArrayLeaseInfo();
    }

    //===============
    // TRANSACTIONS
    //===============

/*
    public <T extends Transaction> Amount calculateTransactionFee(T transaction) throws IOException, NodeException {
        JsonNode json = asJson(post("/transactions/calculateFee").setEntity(new StringEntity(transaction.toJson(), ContentType.APPLICATION_JSON)));
        return Amount.of(json.get("feeAmount").asLong(), JsonSerializer.assetIdFromJson(json.get("feeAssetId")));
    }

    public <T extends Transaction> T broadcast(T transaction) throws IOException, NodeException {
        //noinspection unchecked
        return (T) asType(post("/transactions/broadcast")
                        .setEntity(new StringEntity(transaction.toJson(), ContentType.APPLICATION_JSON)),
                TypeRef.TRANSACTION);
    }

    public EthRpcResponse broadcastEthTransaction(EthereumTransaction ethTransaction) throws IOException, NodeException {
        HttpUriRequest rq = buildSendRawTransactionRq(ethTransaction.toRawHexString());
        ObjectNode rs = sendEthRequest(rq);
        return handleEthResponse(rs);
    }
*/

    function getTransactionInfo( Id $txId ): TransactionInfo
    {
        return $this->get( '/transactions/info/' . $txId->toString() )->asTransactionInfo();
    }

    /**
     * @return array<int, TransactionInfo>
     */
    function getTransactionsByAddress( Address $address, int $limit = 100, Id $afterTxId = null ): array
    {
        $uri = '/transactions/address/' . $address->toString() . '/limit/' . $limit;
        if( isset( $afterTxId ) )
            $uri .= '?after=' . $afterTxId->toString();
        return $this->get( $uri )->get( 0 )->asJson()->asArrayTransactionInfo();
    }

    function getTransactionStatus( Id $txId ): TransactionStatus
    {
        return $this->get( '/transactions/status?id=' . $txId->toString() )->get( 0 )->asJson()->asTransactionStatus();
    }

    /**
     * @param array<int, Id> $txIds
     * @return array<int, TransactionStatus>
     */
    function getTransactionsStatus( array $txIds ): array
    {
        $json = new Json;

        $array = [];
        foreach( $txIds as $txId )
            $array[] = $txId->toString();
        $json->put( 'ids', $array );

        return $this->post( '/transactions/status', $json )->asArrayTransactionStatus();
    }

    function getUnconfirmedTransaction( Id $txId ): Transaction
    {
        return $this->get( '/transactions/unconfirmed/info/' . $txId->toString() )->asTransaction();
    }

    /**
     * @return array<int, Transaction>
     */
    function getUnconfirmedTransactions(): array
    {
        return $this->get( '/transactions/unconfirmed' )->asArrayTransaction();
    }

    function getUtxSize(): int
    {
        return $this->get( '/transactions/unconfirmed/size' )->get( 'size' )->asInt();
    }

    //===============
    // UTILS
    //===============

    function compileScript( string $source, bool $enableCompaction = null ): ScriptInfo
    {
        $uri = '/utils/script/compileCode';
        if( isset( $enableCompaction ) )
            $uri .= '?compact=' . ( $enableCompaction ? 'true' : 'false' );
        return $this->post( $uri, $source )->asScriptInfo();
    }

    function ethToWavesAsset( string $asset ): string
    {
        return $this->get( '/eth/assets?id=' . $asset )->get( 0 )->asJson()->asAssetDetails()->assetId()->encoded();
    }
}

/*

    //===============
    // WAITINGS
    //===============

    private final int blockInterval = 60;

    public TransactionInfo waitForTransaction(Id id, int waitingInSeconds) throws IOException {
        int pollingIntervalInMillis = 100;

        if (waitingInSeconds < 1)
            throw new IllegalStateException("waitForTransaction: waiting value must be positive. Current: " + waitingInSeconds);

        Exception lastException = null;
        for (long spentMillis = 0; spentMillis < waitingInSeconds * 1000L; spentMillis += pollingIntervalInMillis) {
            try {
                return this.getTransactionInfo(id);
            } catch (Exception e) {
                lastException = e;
                try {
                    Thread.sleep(pollingIntervalInMillis);
                } catch (InterruptedException ignored) {
                }
            }
        }
        throw new IOException("Could not wait for transaction " + id + " in " + waitingInSeconds + " seconds", lastException);
    }

    public TransactionInfo waitForTransaction(Id id) throws IOException {
        return waitForTransaction(id, blockInterval);
    }

    public <T extends TransactionInfo> T waitForTransaction(Id id, Class<T> infoClass) throws IOException {
        return infoClass.cast(waitForTransaction(id));
    }

    public void waitForTransactions(List<Id> ids, int waitingInSeconds) throws IOException, NodeException {
        int pollingIntervalInMillis = 1000;

        if (waitingInSeconds < 1)
            throw new IllegalStateException("waitForTransaction: waiting value must be positive. Current: " + waitingInSeconds);

        Exception lastException = null;
        for (long spentMillis = 0; spentMillis < waitingInSeconds * 1000L; spentMillis += pollingIntervalInMillis) {
            try {
                List<TransactionStatus> statuses = this.getTransactionsStatus(ids);
                if (statuses.stream().allMatch(s -> CONFIRMED.equals(s.status())))
                    return;
            } catch (Exception e) {
                lastException = e;
                try {
                    Thread.sleep(pollingIntervalInMillis);
                } catch (InterruptedException ignored) {
                }
            }
        }

        List<TransactionStatus> statuses = this.getTransactionsStatus(ids);
        List<TransactionStatus> unconfirmed =
                statuses.stream().filter(s -> !CONFIRMED.equals(s.status())).collect(toList());
        throw new IOException("Could not wait for " + unconfirmed.size() + " of " + ids.size() +
                " transactions in " + waitingInSeconds + " seconds: " + unconfirmed, lastException);
    }

    public void waitForTransactions(List<Id> ids) throws IOException, NodeException {
        waitForTransactions(ids, blockInterval);
    }

    public void waitForTransactions(Id... ids) throws IOException, NodeException {
        waitForTransactions(asList(ids));
    }

    public int waitForHeight(int target, int waitingInSeconds) throws IOException, NodeException {
        int start = this.getHeight();
        int prev = start;
        int pollingIntervalInMillis = 100;

        if (waitingInSeconds < 1)
            throw new IllegalStateException("waitForHeight: value must be positive. Current: " + waitingInSeconds);

        for (long spentMillis = 0; spentMillis < waitingInSeconds * 1000L; spentMillis += pollingIntervalInMillis) {
            int current = this.getHeight();

            if (current >= target)
                return current;
            else if (current > prev) {
                prev = current;
                spentMillis = 0;
            }

            try {
                Thread.sleep(pollingIntervalInMillis);
            } catch (InterruptedException ignored) {
            }
        }
        throw new IllegalStateException("Could not wait for the height to rise from " + start + " to " + target +
                ": height " + prev + " did not grow for " + waitingInSeconds + " seconds");
    }

    public int waitForHeight(int expectedHeight) throws IOException, NodeException {
        return waitForHeight(expectedHeight, blockInterval * 3);
    }

    public int waitBlocks(int blocksCount, int waitingInSeconds) throws IOException, NodeException {
        if (waitingInSeconds < 1)
            throw new IllegalStateException("waitBlocks: waiting value must be positive. Current: " + waitingInSeconds);
        return waitForHeight(getHeight() + blocksCount, waitingInSeconds);
    }

    public int waitBlocks(int blocksCount) throws IOException, NodeException {
        return waitBlocks(blocksCount, blockInterval * 3);
    }
}
*/