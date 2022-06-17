<?php

require_once 'common.php';

use deemru\ErrCode;
use deemru\Node;

class NodeTest extends PHPUnit\Framework\TestCase
{
    public function testConstruct(): void
    {
        $nodeW = new Node( Node::MAINNET );
        $nodeT = new Node( Node::TESTNET );
        $nodeS = new Node( Node::STAGENET );

        $this->assertSame( 'W', $nodeW->chainId() );
        $this->assertSame( 'T', $nodeT->chainId() );
        $this->assertSame( 'S', $nodeS->chainId() );
        $this->assertSame( '72k1xXWG59fYdzSNoA', deemru\base58Encode( 'Hello, World!' ) );
    }

    private function catchExceptionOrFail( int $code, callable $block ): void
    {
        try
        {
            $block();
            $this->fail( 'Failed to catch exception with code:' . $code );
        }
        catch( Exception $e )
        {
            $this->assertEquals( $code, $e->getCode() );
        }
    }

    public function testExceptions(): void
    {
        $this->catchExceptionOrFail( ErrCode::BASE58_DECODE, function()
        {
            deemru\base58Decode( 'ill' );
        } );
    }
}

if( DO_LOCAL_DEBUG )
{
    $test = new NodeTest;
    $test->testConstruct();
    $test->testExceptions();
}
