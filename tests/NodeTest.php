<?php

require_once 'common.php';

use deemru\Node;

use function PHPUnit\Framework\assertSame;

function testConstruct(): void
{
    $nodeW = new Node( Node::MAINNET );
    $nodeT = new Node( Node::TESTNET );
    $nodeS = new Node( Node::STAGENET );

    assertSame( 'W', $nodeW->chainId() );
    assertSame( 'T', $nodeT->chainId() );
    assertSame( 'S', $nodeS->chainId() );
}

if( DO_LOCAL_DEBUG )
{
    testConstruct();
}

class NodeTest extends PHPUnit\Framework\TestCase
{
    public function testConstruct(): void { testConstruct(); }
}