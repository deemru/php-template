<?php declare( strict_types = 1 );

namespace wavesplatform\API;

use Exception;
use wavesplatform\ExceptionCode;

use deemru\WavesKit;

use wavesplatform\Model\Json;
use wavesplatform\Model\Address;
use wavesplatform\Model\AssetId;
use wavesplatform\Model\AssetDistribution;
use wavesplatform\Model\AssetBalance;
use wavesplatform\Model\AssetDetails;
use wavesplatform\Model\Alias;
use wavesplatform\Model\Balance;
use wavesplatform\Model\BalanceDetails;
use wavesplatform\Model\Base58String;
use wavesplatform\Model\Block;
use wavesplatform\Model\Id;
use wavesplatform\Model\BlockHeaders;
use wavesplatform\Model\BlockchainRewards;
use wavesplatform\Model\ChainId;
use wavesplatform\Model\DataEntry;
use wavesplatform\Model\ScriptInfo;
use wavesplatform\Model\ScriptMeta;

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
     * @param Json|null $json
     * @return Json
     */
    private function fetch( string $uri, Json $json = null )
    {
        if( isset( $json ) )
            $fetch = $this->wk->fetch( $uri, true, $json->toString() );
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
     * @param Json $json
     * @return Json
     */
    function post( string $uri, Json $json ): Json
    {
        return $this->fetch( $uri, $json );
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
}
