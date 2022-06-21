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
     */
    public function __construct( string $uri )
    {
        $this->uri = $uri;
        $this->wk = new \deemru\WavesKit( '?', function( string $wklevel, string $wkmessage )
        {
            $this->wklevel = $wklevel;
            $this->wkmessage = $wkmessage;
        } );
        $this->wk->setNodeAddress( $uri, 0 );
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
     * Fetch a custom REST API request
     *
     * @param string $uri
     * @return array<mixed, mixed>
     */
    public function fetch( string $uri ): array
    {
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
        return $fetch;
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
        $addresses = [];
        foreach( $this->fetch( '/addresses' ) as $address )
        {
            if( !is_string( $address ) )
                throw new Exception( __FUNCTION__ . ' failed to get valid address', ErrCode::STRING_EXPECTED );
            $addresses[] = Address::fromString( $address );
        }
        return $addresses;
    }

    //===============
    // BLOCKS
    //===============

    public function getHeight(): int
    {
        return asInt( $this->fetch( '/blocks/height' ), 'height' );
    }

/*
    public int getBlockHeight(Base58String blockId) throws IOException, NodeException {
        return asJson(get("/blocks/height/" + blockId.toString()))
                .get("height").asInt();
    }

    public int getBlockHeight(long timestamp) throws IOException, NodeException {
        return asJson(get("/blocks/heightByTimestamp/" + timestamp))
                .get("height").asInt();
    }

    public int getBlocksDelay(Base58String startBlockId, int blocksNum) throws IOException, NodeException {
        return asJson(get("/blocks/delay/" + startBlockId.toString() + "/" + blocksNum))
                .get("delay").asInt();
    }
*/
}
