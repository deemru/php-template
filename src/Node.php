<?php declare( strict_types = 1 );

namespace deemru;

require_once __DIR__ . '/common.php';

use Exception;

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
    public function __construct( string $uri, string $chainId = '' )
    {
        $this->uri = $uri;
        $this->wk = new \deemru\WavesKit( '?', function( string $wklevel, string $wkmessage )
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
            $this->chainId = 'W';
        else
        if( $uri === Node::TESTNET )
            $this->chainId = 'T';
        else
        if( $uri === Node::STAGENET )
            $this->chainId = 'S';
        else
            $this->chainId = $this->getAddresses()[0]->chainId();

        $this->wk->chainId = $this->chainId; // @phpstan-ignore-line // accept workaround
    }

    public function chainId(): string
    {
        return $this->chainId;
    }

    public function uri(): string
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
            throw new Exception( $message, ErrCode::FETCH_URI );
        }
        $fetch = $this->wk->json_decode( $fetch );
        if( $fetch === false )
            throw new Exception( __FUNCTION__ . ' failed to decode `' . $uri . '`', ErrCode::JSON_DECODE );
        return asJson( $fetch );
    }

    /**
     * GETs a custom REST API request
     *
     * @param string $uri
     * @return Json
     */
    public function get( string $uri ): Json
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
    public function post( string $uri, Json $json ): Json
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
    public function getAddresses(): array
    {
        return $this->get( '/addresses' )->asArrayAddress();
    }

    /**
     * Return addresses of the node by indexes
     *
     * @return array<int, Address>
     */
    public function getAddressesByIndexes( int $fromIndex, int $toIndex ): array
    {
        return $this->get( '/addresses/seq/' . $fromIndex . '/' . $toIndex )->asArrayAddress();
    }

    public function getBalance( Address $address, int $confirmations = null ): int
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
    public function getBalances( array $addresses, int $height = null ): array
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

    public function getBalanceDetails( Address $address ): BalanceDetails
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
    public function getData( Address $address, string $regex = null ): array
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
    public function getDataByKeys( Address $address, array $keys ): array
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
    public function getDataByKey( Address $address, string $key ): DataEntry
    {
        return $this->get( '/addresses/data/' . $address->toString() . '/' . $key )->asDataEntry();
    }
/*
    public ScriptInfo getScriptInfo(Address address) throws IOException, NodeException {
        return asType(get("/addresses/scriptInfo/" + address.toString()), TypeRef.SCRIPT_INFO);
    }

    public ScriptMeta getScriptMeta(Address address) throws IOException, NodeException {
        JsonNode json = asJson(get("/addresses/scriptInfo/" + address.toString() + "/meta"));
        if (json.hasNonNull("meta"))
            return mapper.convertValue(json.get("meta"), TypeRef.SCRIPT_META);
        else
            return new ScriptMeta(0, new HashMap<>());
    }
*/
    //===============
    // BLOCKS
    //===============

    public function getHeight(): int
    {
        return $this->get( '/blocks/height' )->get( 'height' )->asInt();
    }

    public function getBlockHeightById( string $blockId ): int
    {
        return $this->get( '/blocks/height/' . $blockId )->get( 'height' )->asInt();
    }

    public function getBlockHeightByTimestamp( int $timestamp ): int
    {
        return $this->get( "/blocks/heightByTimestamp/" . $timestamp )->get( "height" )->asInt();
    }

    public function getBlocksDelay( string $startBlockId, int $blocksNum ): int
    {
        return $this->get( "/blocks/delay/" . $startBlockId . "/" . $blocksNum )->get( "delay" )->asInt();
    }

    public function getBlockHeadersByHeight( int $height ): BlockHeaders
    {
        return $this->get( "/blocks/headers/at/" . $height )->asBlockHeaders();
    }

    public function getBlockHeadersById( string $blockId ): BlockHeaders
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
    public function getBlocksHeaders( int $fromHeight, int $toHeight ): array
    {
        return $this->get( "/blocks/headers/seq/" . $fromHeight . "/" . $toHeight )->asArrayBlockHeaders();
    }

    public function getLastBlockHeaders(): BlockHeaders
    {
        return $this->get( "/blocks/headers/last" )->asBlockHeaders();
    }
}
