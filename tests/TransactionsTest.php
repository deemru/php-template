<?php

namespace wavesplatform;

require_once 'common.php';

use deemru\WavesKit;
use Exception;
use wavesplatform\Common\ExceptionCode;

use wavesplatform\Account\Address;
use wavesplatform\Account\PrivateKey;
use wavesplatform\Account\PublicKey;
use wavesplatform\API\Node;
use wavesplatform\Common\Base58String;
use wavesplatform\Common\Base64String;
use wavesplatform\Model\Alias;
use wavesplatform\Model\AssetId;
use wavesplatform\Model\ChainId;
use wavesplatform\Model\LeaseStatus;
use wavesplatform\Model\Id;
use wavesplatform\Model\WavesConfig;
use wavesplatform\Transactions\Amount;
use wavesplatform\Transactions\IssueTransaction;
use wavesplatform\Transactions\Recipient;
use wavesplatform\Transactions\TransferTransaction;
use wavesplatform\Util\Functions;

class TransactionsTest extends \PHPUnit\Framework\TestCase
{
    private ChainId $chainId;
    private Node $node;
    private PrivateKey $account;

    private function prepare(): void
    {
        if( isset( $this->chainId ) )
            return;

        $chainId = ChainId::TESTNET();
        $node = new Node( Node::TESTNET );
        $account = PrivateKey::fromSeed( '10239486123587123659817234612897461289374618273461872468172436812736481274368921763489127436912873649128364' );
        $publicKey = PublicKey::fromPrivateKey( $account );
        $address = Address::fromPublicKey( $publicKey );

        $this->assertSame( $account->publicKey()->toString(), $publicKey->toString() );
        $this->assertSame( $account->publicKey()->address()->toString(), $address->toString() );

        $this->node = $node;
        $this->account = $account;
        $this->chainId = $chainId;

        WavesConfig::chainId( $this->chainId );
    }

    function testIssue(): void
    {
        $this->prepare();
        $chainId = $this->chainId;
        $node = $this->node;
        $account = $this->account;
        $sender = $account->publicKey();

        $tx = IssueTransaction::build(
            $sender,
            'NFT-' . mt_rand( 100000, 999999 ),
            'test  description',
            1,
            0,
            false
        );

        $tx->bodyBytes();

        $id = $tx->id();
        $tx->version();
        $tx->chainId();
        $tx->sender();
        $tx->timestamp();
        $tx->fee();
        $tx->proofs();

        $tx->name();
        $tx->description();
        $tx->quantity();
        $tx->decimals();
        $tx->isReissuable();
        $tx->script();

        $tx1 = $node->waitForTransaction( $node->broadcast( $tx->addProof( $account ) )->id() );

        $this->assertSame( $id->toString(), $tx1->id()->toString() );

        $tx2 = $node->waitForTransaction(
            $node->broadcast(
                (new IssueTransaction)
                ->setName( 'NFT-' . mt_rand( 100000, 999999 ) )
                ->setDescription( 'test description' )
                ->setQuantity( 1 )
                ->setDecimals( 0 )
                ->setIsReissuable( false )

                ->setSender( $sender )
                ->setType( IssueTransaction::TYPE )
                ->setVersion( IssueTransaction::LATEST_VERSION )
                ->setFee( Amount::of( IssueTransaction::NFT_MIN_FEE ) )
                ->setChainId( $chainId )
                ->setTimestamp()

                ->addProof( $account )
            )->id()
        );
        
        $this->assertNotSame( $tx1->id(), $tx2->id() );
    }

    function testTransfer(): void
    {
        $this->prepare();
        $chainId = $this->chainId;
        $node = $this->node;
        $account = $this->account;
        $sender = $account->publicKey();

        $recipient = Recipient::fromAlias( new Alias( 'test' ) );
        $amount = new Amount( 1, AssetId::WAVES() );
        $attachment = Base58String::fromBytes( 'test' );

        $tx = TransferTransaction::build(
            $sender,
            $recipient,
            $amount,
        );

        $tx->bodyBytes();

        $id = $tx->id();
        $tx->version();
        $tx->chainId();
        $tx->sender();
        $tx->timestamp();
        $tx->fee();
        $tx->proofs();

        $tx->recipient();
        $tx->amount();
        $tx->attachment();

        $tx1 = $node->waitForTransaction( $node->broadcast( $tx->addProof( $account ) )->id() );

        $this->assertSame( $id->toString(), $tx1->id()->toString() );

        $tx2 = $node->waitForTransaction(
            $node->broadcast(
                (new TransferTransaction)
                ->setRecipient( Recipient::fromAddress( $node->getAddressByAlias( $recipient->alias() ) ) )
                ->setAmount( $amount )
                ->setAttachment( $attachment )

                ->setSender( $sender )
                ->setType( TransferTransaction::TYPE )
                ->setVersion( TransferTransaction::LATEST_VERSION )
                ->setFee( Amount::of( TransferTransaction::MIN_FEE ) )
                ->setChainId( $chainId )
                ->setTimestamp()

                ->addProof( $account )
            )->id()
        );
        
        $this->assertNotSame( $tx1->id(), $tx2->id() );
    }

    function testMoreCoverage(): void
    {
    }

    function testExceptions(): void
    {
    }
}

if( DO_LOCAL_DEBUG )
{
    $test = new TransactionsTest;
    $test->testIssue();
    $test->testTransfer();
    $test->testMoreCoverage();
    $test->testExceptions();
}
