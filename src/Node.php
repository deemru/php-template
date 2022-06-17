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

    /**
     * Creates Node instance
     *
     * @param string $uri Node REST API address
     */
    public function __construct( string $uri )
    {
        $this->uri = $uri;
        $this->wk = new \deemru\WavesKit;
        $this->wk->setNodeAddress( $uri, 0 );
        $this->chainId = $this->getAddresses()[0]->chainId();
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
            throw new Exception( __FUNCTION__ . ' failed to fetch data at ' . $uri );
        $data = $this->wk->json_decode( $fetch );
        if( $data === false )
            throw new Exception( __FUNCTION__ . ' failed to decode data at ' . $uri );
        return $data;
    }

    /**
     * Return addresses of the node
     *
     * @return array<int, Address>
     */
    public function getAddresses(): array
    {
        $addresses = [];
        foreach( $this->fetch( '/addresses' ) as $address )
            $addresses[] = Address::fromString( $address );
        return $addresses;
    }

    public function chainId(): string
    {
        return $this->chainId;
    }

    public function uri(): string
    {
        return $this->uri;
    }
}
