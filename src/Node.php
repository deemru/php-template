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
     * Gets a custom REST API request
     *
     * @param string $uri
     * @return array<mixed, mixed>
     */
    public function get( string $uri ): array
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
        foreach( $this->get( '/addresses' ) as $address )
            $addresses[] = Address::fromString( asValue( $address )->asString() );
        return $addresses;
    }

    //===============
    // BLOCKS
    //===============

    public function getHeight(): int
    {
        return asJson( $this->get( '/blocks/height' ) )->get( 'height' )->asInt();
    }

    public function getBlockHeightById( string $blockId ): int
    {
        return asJson( $this->get( '/blocks/height/' . $blockId ) )->get( 'height' )->asInt();
    }

    public function getBlockHeightByTimestamp( int $timestamp ): int
    {
        return asJson( $this->get( "/blocks/heightByTimestamp/" . $timestamp ) )->get( "height" )->asInt();
    }

    public function getBlocksDelay( string $startBlockId, int $blocksNum ): int
    {
        return asJson( $this->get( "/blocks/delay/" . $startBlockId . "/" . $blocksNum ) )->get( "delay" )->asInt();
    }
}
