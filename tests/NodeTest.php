<?php

namespace wavesplatform;

require_once 'common.php';

use Exception;
use wavesplatform\ExceptionCode;

use wavesplatform\API\Node;
use wavesplatform\Model\Address;
use wavesplatform\Model\ChainId;
use wavesplatform\Util\Functions;

class NodeTest extends \PHPUnit\Framework\TestCase
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

    function testNode(): void
    {
        $nodeW = new Node( Node::MAINNET );
        $nodeT = new Node( Node::TESTNET );
        $nodeS = new Node( Node::STAGENET );

        
        $addressT = Address::fromString( '3N9WtaPoD1tMrDZRG26wA142Byd35tLhnLU' );
        $aliases = $nodeT->getAliasesByAddress( $addressT );
        foreach( $aliases as $alias )
        {
            $alias->name();
            $alias->bytes();
            $this->assertSame( $addressT->encoded(), $nodeT->getAddressByAlias( $alias )->encoded() );
            $alias->toString();
        }

        $addressT = Address::fromString( '3N7uoMNjqNt1jf9q9f9BSr7ASk1QtzJABEY' );

        $scriptInfo = $nodeT->getScriptInfo( $addressT );
        $scriptInfo->script();
        $scriptInfo->complexity();
        $scriptInfo->extraFee();
        $scriptInfo->verifierComplexity();
        $mapComplexities = $scriptInfo->callableComplexities();

        $scriptMeta = $nodeT->getScriptMeta( $addressT );
        $version = $scriptMeta->metaVersion();
        $funcs = $scriptMeta->callableFunctions();
        foreach( $funcs as $func => $args )
            foreach( $args as $arg )
            {
                $arg->name();
                $arg->type();
            }

        $addressT = Address::fromString( '3NAV8CuN5Zn6TT1gChFM2wXRtdhUBDUtCVt' );

        $dataEntries1 = $nodeT->getData( $addressT, 'key_\d' );
        $dataEntries2 = $nodeT->getData( $addressT );

        $this->assertLessThan( count( $dataEntries2 ), count( $dataEntries1 ) );

        $keys = [];
        foreach( $dataEntries1 as $dataEntry )
            $keys[] = $dataEntry->key();

        $dataEntries2 = $nodeT->getDataByKeys( $addressT, $keys );
        $this->assertSame( count( $dataEntries1 ), count( $dataEntries2 ) );

        $n = count( $dataEntries1 );
        for( $i = 0; $i < $n; ++$i )
            $this->assertSame( $dataEntries1[$i]->value(), $dataEntries2[$i]->value() );

        $this->assertSame( ChainId::MAINNET, $nodeW->chainId() );
        $this->assertSame( ChainId::TESTNET, $nodeT->chainId() );
        $this->assertSame( ChainId::STAGENET, $nodeS->chainId() );

        $this->assertSame( $nodeW->uri(), Node::MAINNET );
        $this->assertSame( $nodeT->uri(), Node::TESTNET );
        $this->assertSame( $nodeS->uri(), Node::STAGENET );

        $heightW = $nodeW->getHeight();
        $heightT = $nodeT->getHeight();
        $heightS = $nodeS->getHeight();

        $this->assertLessThan( $heightW, $heightT );
        $this->assertLessThan( $heightT, $heightS );
        $this->assertLessThan( $heightS, 1 );

        $addresses = $nodeW->getAddresses();

        $address1 = $addresses[0];
        $address2 = $nodeW->getAddressesByIndexes( 0, 1 )[0];

        $this->assertSame( $address1->encoded(), Functions::base58Encode( $address2->bytes() ) );

        $balance1 = $nodeW->getBalance( $address1 );
        $balance2 = $nodeW->getBalance( $address2, 0 );

        $this->assertSame( $balance1, $balance2 );

        $balances = $nodeW->getBalances( $addresses );

        $balance1 = $balances[0];
        $balance2 = $nodeW->getBalances( $addresses, $heightW )[0];

        $this->assertSame( $balance1->getAddress(), $balance2->getAddress() );
        $this->assertSame( $balance1->getBalance(), $balance2->getBalance() );

        $balanceDetails = $nodeW->getBalanceDetails( $address1 );

        $this->assertSame( $balanceDetails->address(), $address1->toString() );
        $this->assertSame( $balanceDetails->available(), $balance1->getBalance() );
        $balanceDetails->effective();
        $balanceDetails->generating();
        $balanceDetails->regular();        

        $headers = $nodeW->getLastBlockHeaders();
        $headers = $nodeW->getBlockHeadersByHeight( $headers->height() - 10 );

        $headers->baseTarget();
        $headers->desiredReward();
        $headers->features();
        $headers->generationSignature();
        $headers->generator();
        $headers->height();
        $headers->id();
        $headers->reference();
        $headers->reward();
        $headers->signature();
        $headers->size();
        $headers->timestamp();
        $headers->totalFee();
        $headers->transactionsCount();
        $headers->transactionsRoot();
        $headers->version();
        $headers->vrf();

        $height1 = $nodeW->getBlockHeightById( $headers->id() );
        $height2 = $nodeW->getBlockHeightByTimestamp( $headers->timestamp() );

        $this->assertSame( $headers->height(), $height1 );
        $this->assertSame( $headers->height(), $height2 );

        $headers1 = $nodeW->getBlockHeadersByHeight( $headers->height() );
        $headers2 = $nodeW->getBlockHeadersById( $headers->id() );
        $headers3 = $nodeW->getBlocksHeaders( $headers->height() - 1, $headers->height() )[1];
        $this->assertSame( $headers->toString(), $headers1->toString() );
        $this->assertSame( $headers->toString(), $headers2->toString() );
        $this->assertSame( $headers->toString(), $headers3->toString() );

        $delay = $nodeW->getBlocksDelay( $nodeW->getBlockHeadersByHeight( $headers->height() - 200 )->id(), 100 );
        $this->assertLessThan( 70 * 1000, $delay );
        $this->assertLessThan( $delay, 50 * 1000 );
    }

    function testMoreCoverage(): void
    {
        $node1 = new Node( Node::MAINNET, 'W' );
        $node2 = new Node( Node::MAINNET, '?' );
        $node3 = new Node( str_replace( 'https', 'http', Node::MAINNET ) );
        $this->assertSame( $node1->chainId(), $node2->chainId() );
        $this->assertSame( $node2->chainId(), $node3->chainId() );
    }

    function testExceptions(): void
    {
        $node = new Node( Node::MAINNET );
        $json = $node->get( '/blocks/headers/last' );

        $this->catchExceptionOrFail( ExceptionCode::BASE58_DECODE, function(){ Functions::base58Decode( 'ill' ); } );
        $this->catchExceptionOrFail( ExceptionCode::FETCH_URI, function() use ( $node ){ $node->get( '/test' ); } );
        $this->catchExceptionOrFail( ExceptionCode::JSON_DECODE, function() use ( $node ){ $node->get( '/api-docs/favicon-16x16.png' ); } );
        $this->catchExceptionOrFail( ExceptionCode::KEY_MISSING, function() use ( $json ){ $json->get( 'x' ); } );
        $this->catchExceptionOrFail( ExceptionCode::INT_EXPECTED, function() use ( $json ){ $json->get( 'signature' )->asInt(); } );
        $this->catchExceptionOrFail( ExceptionCode::STRING_EXPECTED, function() use ( $json ){ $json->get( 'height' )->asString(); } );
        $this->catchExceptionOrFail( ExceptionCode::ARRAY_EXPECTED, function() use ( $json ){ $json->get( 'height' )->asArrayInt(); } );
    }
}

if( DO_LOCAL_DEBUG )
{
    $test = new NodeTest;
    $test->testNode();
    $test->testMoreCoverage();
    $test->testExceptions();
}
