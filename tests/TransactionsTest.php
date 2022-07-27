<?php

namespace wavesplatform;

require_once 'common.php';

use deemru\WavesKit;
use Exception;
use Waves\SetScriptTransactionData;
use wavesplatform\Common\ExceptionCode;

use wavesplatform\Account\Address;
use wavesplatform\Account\PrivateKey;
use wavesplatform\Account\PublicKey;
use wavesplatform\API\Node;
use wavesplatform\Common\Base58String;
use wavesplatform\Common\Base64String;
use wavesplatform\Model\Alias;
use wavesplatform\Model\ApplicationStatus;
use wavesplatform\Model\AssetId;
use wavesplatform\Model\ChainId;
use wavesplatform\Model\LeaseStatus;
use wavesplatform\Model\Id;
use wavesplatform\Model\WavesConfig;
use wavesplatform\Transactions\Amount;
use wavesplatform\Transactions\BurnTransaction;
use wavesplatform\Transactions\IssueTransaction;
use wavesplatform\Transactions\Recipient;
use wavesplatform\Transactions\ReissueTransaction;
use wavesplatform\Transactions\SetAssetScriptTransaction;
use wavesplatform\Transactions\SponsorFeeTransaction;
use wavesplatform\Transactions\TransferTransaction;
use wavesplatform\Util\Functions;

class TransactionsTest extends \PHPUnit\Framework\TestCase
{
    private ChainId $chainId;
    private Node $node;
    private PrivateKey $account;
    private AssetId $sponsorId;
    private AssetId $tokenId;

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

        $this->prepareSponsor();
        $this->prepareToken();
    }

    private function prepareSponsor(): void
    {
        if( 1 ) // @phpstan-ignore-line // fast/full test
        {
            $this->sponsorId = AssetId::fromString( '7xaTA288aL96de84ppHbKXxe2uh8FpypgNGJuHz1jtQ8' );
        }
        else
        {
            $this->prepare();
            $chainId = $this->chainId;
            $node = $this->node;
            $account = $this->account;
            $sender = $account->publicKey();

            $tx = $node->waitForTransaction( $node->broadcast( IssueTransaction::build( $sender, 'SPONSOR', '', 1, 0, false )->addProof( $account ) )->id() );
            $this->sponsorId = AssetId::fromString( $tx->id()->toString() );
        }
    }

    private function prepareToken(): void
    {
        if( 1 ) // @phpstan-ignore-line // fast/full test
        {
            $this->tokenId = AssetId::fromString( '8whRxvA9Rs6b7bb5AZheEijobEqJMCT96hZiQcJPgP9D' );
        }
        else
        {
            $this->prepare();
            $chainId = $this->chainId;
            $node = $this->node;
            $account = $this->account;
            $sender = $account->publicKey();

            $tx = $node->waitForTransaction( $node->broadcast( IssueTransaction::build( $sender, 'TOKEN', '', 1000000, 6, true )->addProof( $account ) )->id() );
            $this->tokenId = AssetId::fromString( $tx->id()->toString() );
        }
    }

    function testSetAssetScript(): void
    {
        $this->prepare();
        $chainId = $this->chainId;
        $node = $this->node;
        $account = $this->account;
        $sender = $account->publicKey();

        if( $node->getBalance( $sender->address() ) < 10_00000000 ) // TODO: faucet
        {
            $this->assertNotSame( $this->tokenId->toString(), $this->sponsorId->toString() );
            return;
        }        

        $script = Base64String::fromString( 'BQbtKNoM' );

        $tx = $node->waitForTransaction( $node->broadcast( IssueTransaction::build( $sender, 'SCRIPTED', '', 1, 0, false, $script )->addProof( $account ) )->id() );
        $scriptedId = AssetId::fromString( $tx->id()->toString() );

        $tx = SetAssetScriptTransaction::build(
            $sender,
            $scriptedId,
            $script
        );

        $tx->bodyBytes();

        $id = $tx->id();
        $tx->version();
        $tx->chainId();
        $tx->sender();
        $tx->timestamp();
        $tx->fee();
        $tx->proofs();

        $tx->assetId();
        $tx->script();

        $tx1 = $node->waitForTransaction( $node->broadcast( $tx->addProof( $account ) )->id() );

        $this->assertSame( $id->toString(), $tx1->id()->toString() );
        $this->assertSame( $tx1->applicationStatus(), ApplicationStatus::SUCCEEDED );

        $tx2 = $node->waitForTransaction(
            $node->broadcast(
                (new SetAssetScriptTransaction)
                ->setAssetId( $scriptedId )
                ->setScript( $script )

                ->setSender( $sender )
                ->setType( SetAssetScriptTransaction::TYPE )
                ->setVersion( SetAssetScriptTransaction::LATEST_VERSION )
                ->setFee( Amount::of( SetAssetScriptTransaction::MIN_FEE ) )
                ->setChainId( $chainId )
                ->setTimestamp()

                ->addProof( $account )
            )->id()
        );
        
        $this->assertNotSame( $tx1->id(), $tx2->id() );
        $this->assertSame( $tx2->applicationStatus(), ApplicationStatus::SUCCEEDED );
    }

    function testReissue(): void
    {
        $this->prepare();
        $chainId = $this->chainId;
        $node = $this->node;
        $account = $this->account;
        $sender = $account->publicKey();

        $tx = ReissueTransaction::build(
            $sender,
            Amount::of( 1000_000_000, $this->tokenId ),
            true
        );

        $tx->bodyBytes();

        $id = $tx->id();
        $tx->version();
        $tx->chainId();
        $tx->sender();
        $tx->timestamp();
        $tx->fee();
        $tx->proofs();

        $tx->amount();
        $tx->isReissuable();

        $tx1 = $node->waitForTransaction( $node->broadcast( $tx->addProof( $account ) )->id() );

        $this->assertSame( $id->toString(), $tx1->id()->toString() );
        $this->assertSame( $tx1->applicationStatus(), ApplicationStatus::SUCCEEDED );

        $tx2 = $node->waitForTransaction(
            $node->broadcast(
                (new ReissueTransaction)
                ->setAmount( Amount::of( 2000_000_000, $this->tokenId ) )
                ->setIsReissuable( true )

                ->setSender( $sender )
                ->setType( ReissueTransaction::TYPE )
                ->setVersion( ReissueTransaction::LATEST_VERSION )
                ->setFee( Amount::of( ReissueTransaction::MIN_FEE ) )
                ->setChainId( $chainId )
                ->setTimestamp()

                ->addProof( $account )
            )->id()
        );
        
        $this->assertNotSame( $tx1->id(), $tx2->id() );
        $this->assertSame( $tx2->applicationStatus(), ApplicationStatus::SUCCEEDED );
    }

    function testBurn(): void
    {
        $this->prepare();
        $chainId = $this->chainId;
        $node = $this->node;
        $account = $this->account;
        $sender = $account->publicKey();

        $tx = BurnTransaction::build(
            $sender,
            Amount::of( 100_000_000, $this->tokenId )
        );

        $tx->bodyBytes();

        $id = $tx->id();
        $tx->version();
        $tx->chainId();
        $tx->sender();
        $tx->timestamp();
        $tx->fee();
        $tx->proofs();

        $tx->amount();

        $tx1 = $node->waitForTransaction( $node->broadcast( $tx->addProof( $account ) )->id() );

        $this->assertSame( $id->toString(), $tx1->id()->toString() );
        $this->assertSame( $tx1->applicationStatus(), ApplicationStatus::SUCCEEDED );

        $tx2 = $node->waitForTransaction(
            $node->broadcast(
                (new BurnTransaction)
                ->setAmount( Amount::of( 10_000_000, $this->tokenId ) )

                ->setSender( $sender )
                ->setType( BurnTransaction::TYPE )
                ->setVersion( BurnTransaction::LATEST_VERSION )
                ->setFee( Amount::of( BurnTransaction::MIN_FEE ) )
                ->setChainId( $chainId )
                ->setTimestamp()

                ->addProof( $account )
            )->id()
        );
        
        $this->assertNotSame( $tx1->id(), $tx2->id() );
        $this->assertSame( $tx2->applicationStatus(), ApplicationStatus::SUCCEEDED );
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
        $this->assertSame( $tx1->applicationStatus(), ApplicationStatus::SUCCEEDED );

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
        $this->assertSame( $tx2->applicationStatus(), ApplicationStatus::SUCCEEDED );
    }

    function testSponsorship(): void
    {
        $this->prepare();
        $chainId = $this->chainId;
        $node = $this->node;
        $account = $this->account;
        $sender = $account->publicKey();

        $sponsorId = $this->sponsorId;

        $tx = SponsorFeeTransaction::build(
            $sender,
            $sponsorId,
            1
        );

        $tx->bodyBytes();

        $id = $tx->id();
        $tx->version();
        $tx->chainId();
        $tx->sender();
        $tx->timestamp();
        $tx->fee();
        $tx->proofs();

        $tx->assetId();
        $tx->minSponsoredFee();

        $tx1 = $node->waitForTransaction( $node->broadcast( $tx->addProof( $account ) )->id() );

        $this->assertSame( $id->toString(), $tx1->id()->toString() );
        $this->assertSame( $tx1->applicationStatus(), ApplicationStatus::SUCCEEDED );

        $tx2 = $node->waitForTransaction(
            $node->broadcast(
                (new SponsorFeeTransaction())
                ->setAssetId( $sponsorId )
                ->setMinSponsoredFee( 1 )

                ->setSender( $sender )
                ->setType( SponsorFeeTransaction::TYPE )
                ->setVersion( SponsorFeeTransaction::LATEST_VERSION )
                ->setFee( Amount::of( SponsorFeeTransaction::MIN_FEE ) )
                ->setChainId( $chainId )
                ->setTimestamp()

                ->addProof( $account )
            )->id()
        );
        
        $this->assertNotSame( $tx1->id(), $tx2->id() );
        $this->assertSame( $tx2->applicationStatus(), ApplicationStatus::SUCCEEDED );
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
        )->setFee( Amount::of( 1, $this->sponsorId ) );

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
        $this->assertSame( $tx1->applicationStatus(), ApplicationStatus::SUCCEEDED );

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
        $this->assertSame( $tx2->applicationStatus(), ApplicationStatus::SUCCEEDED );
    }
}

if( DO_LOCAL_DEBUG )
{
    $test = new TransactionsTest;
    $test->testIssue();
    $test->testReissue();
    $test->testBurn();
    $test->testSetAssetScript();
    $test->testSponsorship();
    $test->testTransfer();
}
