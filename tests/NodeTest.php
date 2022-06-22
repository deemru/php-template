<?php

require_once 'common.php';

use deemru\ErrCode;
use deemru\Node;

class NodeTest extends PHPUnit\Framework\TestCase
{
    private function catchExceptionOrFail( int $code, callable $block ): void
    {
        try
        {
            $block();
            $this->fail( 'Failed to catch exception with code:' . $code );
        }
        catch( Exception $e )
        {
            $this->assertEquals( $code, $e->getCode(), $e->getMessage() );
        }
    }

    public function testConstruct(): void
    {
        $nodeW = new Node( Node::MAINNET );
        $nodeT = new Node( Node::TESTNET );
        $nodeS = new Node( Node::STAGENET );

        $this->assertSame( 'W', $nodeW->chainId() );
        $this->assertSame( 'T', $nodeT->chainId() );
        $this->assertSame( 'S', $nodeS->chainId() );

        $this->assertSame( $nodeW->uri(), Node::MAINNET );
        $this->assertSame( $nodeT->uri(), Node::TESTNET );
        $this->assertSame( $nodeS->uri(), Node::STAGENET );

        $heightW = $nodeW->getHeight();
        $heightT = $nodeT->getHeight();
        $heightS = $nodeS->getHeight();

        $this->assertLessThan( $heightW, $heightT );
        $this->assertLessThan( $heightT, $heightS );
        $this->assertLessThan( $heightS, 1 );

        $this->assertSame( '72k1xXWG59fYdzSNoA', deemru\base58Encode( 'Hello, World!' ) );
    }

    public function testMoreCoverage(): void
    {
        $node1 = new Node( Node::MAINNET, '?' );
        $node2 = new Node( str_replace( 'https', 'http', Node::MAINNET ) );

        $this->assertSame( $node1->chainId(), $node2->chainId() );
    }

    public function testExceptions(): void
    {
        $node = new Node( Node::MAINNET );

        $this->catchExceptionOrFail( ErrCode::BASE58_DECODE, function()
        {
            deemru\base58Decode( 'ill' );
        } );

        $this->catchExceptionOrFail( ErrCode::FETCH_URI, function() use ( $node )
        {
            $node->fetch( '/test' );
        } );

        $this->catchExceptionOrFail( ErrCode::JSON_DECODE, function() use ( $node )
        {
            $node->fetch( '/api-docs/favicon-16x16.png' );
        } );

        $this->catchExceptionOrFail( ErrCode::KEY_MISSING, function() use ( $node )
        {
            deemru\getInt( $node->fetch( '/addresses' ), '123' );
        } );

        $this->catchExceptionOrFail( ErrCode::INT_EXPECTED, function() use ( $node )
        {
            deemru\getInt( $node->fetch( '/blocks/headers/last' ), 'signature' );
        } );

        $this->catchExceptionOrFail( ErrCode::STRING_EXPECTED, function() use ( $node )
        {
            deemru\getString( $node->fetch( '/blocks/height' ), 'height' );
        } );
    }
}

if( DO_LOCAL_DEBUG )
{
    $test = new NodeTest;
    $test->testConstruct();
    $test->testMoreCoverage();
    $test->testExceptions();
}
