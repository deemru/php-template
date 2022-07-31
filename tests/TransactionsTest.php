<?php

namespace Waves;

require_once 'common.php';

use deemru\WavesKit;
use Exception;
use Waves\SetScriptTransactionData;
use Waves\Common\ExceptionCode;

use Waves\Account\Address;
use Waves\Account\PrivateKey;
use Waves\Account\PublicKey;
use Waves\API\Node;
use Waves\Common\Base58String;
use Waves\Common\Base64String;
use Waves\Common\Value;
use Waves\Model\Alias;
use Waves\Model\ApplicationStatus;
use Waves\Model\AssetId;
use Waves\Model\ChainId;
use Waves\Model\DataEntry;
use Waves\Model\EntryType;
use Waves\Model\LeaseStatus;
use Waves\Model\Id;
use Waves\Model\WavesConfig;
use Waves\Transactions\Amount;
use Waves\Transactions\BurnTransaction;
use Waves\Transactions\CreateAliasTransaction;
use Waves\Transactions\DataTransaction;
use Waves\Transactions\Invocation\Arg;
use Waves\Transactions\Invocation\Func;
use Waves\Transactions\InvokeScriptTransaction;
use Waves\Transactions\IssueTransaction;
use Waves\Transactions\LeaseCancelTransaction;
use Waves\Transactions\LeaseTransaction;
use Waves\Transactions\Mass\Transfer;
use Waves\Transactions\MassTransferTransaction;
use Waves\Transactions\Recipient;
use Waves\Transactions\ReissueTransaction;
use Waves\Transactions\SetAssetScriptTransaction;
use Waves\Transactions\SetScriptTransaction;
use Waves\Transactions\SponsorFeeTransaction;
use Waves\Transactions\TransferTransaction;
use Waves\Transactions\UpdateAssetInfoTransaction;
use Waves\Util\Functions;

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
        //$chainId = ChainId::STAGENET();
        WavesConfig::chainId( $chainId );

        $node = new Node( Node::TESTNET );
        //$node = new Node( Node::STAGENET );
        $account = PrivateKey::fromSeed( '10239486123587123659817234612897461289374618273461872468172436812736481274368921763489127436912873649128364' );
        $publicKey = PublicKey::fromPrivateKey( $account );
        $address = Address::fromPublicKey( $publicKey );

        $this->assertSame( $account->publicKey()->toString(), $publicKey->toString() );
        $this->assertSame( $account->publicKey()->address()->toString(), $address->toString() );

        $this->node = $node;
        $this->account = $account;
        $this->chainId = $chainId;

        $this->prepareSponsor();
        $this->prepareToken();
    }

    private function prepareSponsor(): void
    {
        if( 1 ) // @phpstan-ignore-line // fast/full test
        {
            $this->sponsorId = AssetId::fromString( '2exRLtCQNwnYpeP17MevNirHJp2u2mtLk7McxyPFcvp5' );
            //$this->sponsorId = AssetId::fromString( '7WmVG9EXb6adXbySyGi313pbFeSCuoPHNVnPpEBy4aYh' ); // stage
        }
        else
        {
            $this->prepare();
            $chainId = $this->chainId;
            $node = $this->node;
            $account = $this->account;
            $sender = $account->publicKey();

            $tx = $node->waitForTransaction( $node->broadcast( IssueTransaction::build( $sender, 'SPONSOR', '', 1000, 0, false )->addProof( $account ) )->id() );
            $this->sponsorId = AssetId::fromString( $tx->id()->toString() );
        }
    }

    private function prepareToken(): void
    {
        if( 1 ) // @phpstan-ignore-line // fast/full test
        {
            $this->tokenId = AssetId::fromString( 'CcK2rmEDNET8iPSyXvZprRPsDt7mfJPbxuvkMRdsyESC' );
            //$this->tokenId = AssetId::fromString( 'BmRLpCbcJ3Xtuev1yJRqzPYdqp9iPFTqW3RJeU4WZMKU' ); // stage
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

    function testAlias(): void
    {
        $this->prepare();
        $chainId = $this->chainId;
        $node = $this->node;
        $account = $this->account;
        $sender = $account->publicKey();

        $tx = CreateAliasTransaction::build(
            $sender,
            Alias::fromString( 'name-' . mt_rand( 10000000000, 99999999999 ) )
        );

        $tx->bodyBytes();

        $id = $tx->id();
        $tx->version();
        $tx->chainId();
        $tx->sender();
        $tx->timestamp();
        $tx->fee();
        $tx->proofs();

        $tx->alias();

        $tx1 = $node->waitForTransaction( $node->broadcast( $tx->addProof( $account ) )->id() );

        $this->assertSame( $id->toString(), $tx1->id()->toString() );
        $this->assertSame( $tx1->applicationStatus(), ApplicationStatus::SUCCEEDED );

        $tx2 = $node->waitForTransaction(
            $node->broadcast(
                (new CreateAliasTransaction)
                ->setAlias( Alias::fromString( 'name-' . mt_rand( 10000000000, 99999999999 ) ) )

                ->setSender( $sender )
                ->setType( CreateAliasTransaction::TYPE )
                ->setVersion( CreateAliasTransaction::LATEST_VERSION )
                ->setFee( Amount::of( CreateAliasTransaction::MIN_FEE ) )
                ->setChainId( $chainId )
                ->setTimestamp()

                ->addProof( $account )
            )->id()
        );

        $this->assertNotSame( $tx1->id(), $tx2->id() );
        $this->assertSame( $tx2->applicationStatus(), ApplicationStatus::SUCCEEDED );
    }

    function testLeaseAndLeaseCancel(): void
    {
        $this->prepare();
        $chainId = $this->chainId;
        $node = $this->node;
        $account = $this->account;
        $sender = $account->publicKey();

        $recipient = Recipient::fromAddressOrAlias( 'test' );

        // LEASE

        $tx = LeaseTransaction::build(
            $sender,
            $recipient,
            10_000_000
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

        $tx1 = $node->waitForTransaction( $node->broadcast( $tx->addProof( $account ) )->id() );

        $this->assertSame( $id->toString(), $tx1->id()->toString() );
        $this->assertSame( $tx1->applicationStatus(), ApplicationStatus::SUCCEEDED );

        $tx2 = $node->waitForTransaction(
            $node->broadcast(
                (new LeaseTransaction)
                ->setRecipient( Recipient::fromAddress( $node->getAddressByAlias( $recipient->alias() ) ) )
                ->setAmount( 20_000_000 )

                ->setSender( $sender )
                ->setType( LeaseTransaction::TYPE )
                ->setVersion( LeaseTransaction::LATEST_VERSION )
                ->setFee( Amount::of( LeaseTransaction::MIN_FEE ) )
                ->setChainId( $chainId )
                ->setTimestamp()

                ->addProof( $account )
            )->id()
        );

        $this->assertNotSame( $tx1->id(), $tx2->id() );
        $this->assertSame( $tx2->applicationStatus(), ApplicationStatus::SUCCEEDED );

        // LEASE_CANCEL

        $tx = LeaseCancelTransaction::build(
            $sender,
            $tx1->id()
        );

        $tx->bodyBytes();

        $id = $tx->id();
        $tx->version();
        $tx->chainId();
        $tx->sender();
        $tx->timestamp();
        $tx->fee();
        $tx->proofs();

        $tx->leaseId();

        $tx1 = $node->waitForTransaction( $node->broadcast( $tx->addProof( $account ) )->id() );

        $this->assertSame( $id->toString(), $tx1->id()->toString() );
        $this->assertSame( $tx1->applicationStatus(), ApplicationStatus::SUCCEEDED );

        $tx2 = $node->waitForTransaction(
            $node->broadcast(
                (new LeaseCancelTransaction)
                ->setLeaseId( $tx2->id() )

                ->setSender( $sender )
                ->setType( LeaseCancelTransaction::TYPE )
                ->setVersion( LeaseCancelTransaction::LATEST_VERSION )
                ->setFee( Amount::of( LeaseCancelTransaction::MIN_FEE ) )
                ->setChainId( $chainId )
                ->setTimestamp()

                ->addProof( $account )
            )->id()
        );

        $this->assertNotSame( $tx1->id(), $tx2->id() );
        $this->assertSame( $tx2->applicationStatus(), ApplicationStatus::SUCCEEDED );
    }

    function testSetScript(): void
    {
        $this->prepare();
        $chainId = $this->chainId;
        $node = $this->node;
        $account = $this->account;
        $sender = $account->publicKey();

        $script = Base64String::fromString( 'AAIFAAAAAAAAAAcIAhIDCgEfAAAAAAAAAAEAAAABaQEAAAAEY2FsbAAAAAEAAAAEbGlzdAUAAAADbmlsAAAAAQAAAAJ0eAEAAAAGdmVyaWZ5AAAAAAkACcgAAAADCAUAAAACdHgAAAAJYm9keUJ5dGVzCQABkQAAAAIIBQAAAAJ0eAAAAAZwcm9vZnMAAAAAAAAAAAAIBQAAAAJ0eAAAAA9zZW5kZXJQdWJsaWNLZXmQFHRt' );

        $tx = SetScriptTransaction::build(
            $sender,
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

        $tx->script();

        $tx1 = $node->waitForTransaction( $node->broadcast( $tx->addProof( $account ) )->id() );

        $this->assertSame( $id->toString(), $tx1->id()->toString() );
        $this->assertSame( $tx1->applicationStatus(), ApplicationStatus::SUCCEEDED );

        $tx2 = $node->waitForTransaction(
            $node->broadcast(
                (new SetScriptTransaction)
                ->setScript() // remove script

                ->setSender( $sender )
                ->setType( SetScriptTransaction::TYPE )
                ->setVersion( SetScriptTransaction::LATEST_VERSION )
                ->setFee( Amount::of( SetScriptTransaction::MIN_FEE ) )
                ->setChainId( $chainId )
                ->setTimestamp()

                ->addProof( $account )
            )->id()
        );

        $this->assertNotSame( $tx1->id(), $tx2->id() );
        $this->assertSame( $tx2->applicationStatus(), ApplicationStatus::SUCCEEDED );
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

    function testRename(): void
    {
        $this->prepare();
        $chainId = $this->chainId;
        $node = $this->node;
        $account = $this->account;
        $sender = $account->publicKey();

        if( $node->getBalance( $sender->address() ) < 10_00000000 ) // TODO: private net
        {
            $this->assertNotSame( $this->tokenId->toString(), $this->sponsorId->toString() );
            return;
        }

        $tokenId = $this->tokenId;

        $tx = UpdateAssetInfoTransaction::build(
            $sender,
            $tokenId,
            'TOKEN-RENAMED',
            'renamed description'
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
        $tx->name();
        $tx->description();

        $tx1 = $node->waitForTransaction( $node->broadcast( $tx->addProof( $account ) )->id() );

        $this->assertSame( $id->toString(), $tx1->id()->toString() );
        $this->assertSame( $tx1->applicationStatus(), ApplicationStatus::SUCCEEDED );

        $tx2 = $node->waitForTransaction(
            $node->broadcast(
                (new UpdateAssetInfoTransaction)
                ->setAssetId( $this->sponsorId )
                ->setName( 'SPONSOR-RENAMED' )
                ->setDescription( 'renamed description' )

                ->setSender( $sender )
                ->setType( UpdateAssetInfoTransaction::TYPE )
                ->setVersion( UpdateAssetInfoTransaction::LATEST_VERSION )
                ->setFee( Amount::of( UpdateAssetInfoTransaction::MIN_FEE ) )
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

    function testData(): void
    {
        $this->prepare();
        $chainId = $this->chainId;
        $node = $this->node;
        $account = $this->account;
        $sender = $account->publicKey();

        $data = [];
        $data[] = DataEntry::build( 'key_string', EntryType::STRING, '123' );
        $data[] = DataEntry::build( 'key_binary', EntryType::BINARY, '123' );
        $data[] = DataEntry::build( 'key_boolean', EntryType::BOOLEAN, true );
        $data[] = DataEntry::build( 'key_integer', EntryType::INTEGER, 123 );
        $data[] = DataEntry::build( 'key_delete', EntryType::DELETE );

        $tx = DataTransaction::build(
            $sender,
            $data
        );

        $tx->bodyBytes();

        $id = $tx->id();
        $tx->version();
        $tx->chainId();
        $tx->sender();
        $tx->timestamp();
        $tx->fee();
        $tx->proofs();

        $tx->data();

        $tx1 = $node->waitForTransaction( $node->broadcast( $tx->addProof( $account ) )->id() );

        $this->assertSame( $id->toString(), $tx1->id()->toString() );
        $this->assertSame( $tx1->applicationStatus(), ApplicationStatus::SUCCEEDED );

        $tx2 = $node->waitForTransaction(
            $node->broadcast(
                (new DataTransaction)
                ->setData( $data )

                ->setSender( $sender )
                ->setType( DataTransaction::TYPE )
                ->setVersion( DataTransaction::LATEST_VERSION )
                ->setFee( Amount::of( DataTransaction::MIN_FEE ) )
                ->setChainId( $chainId )
                ->setTimestamp()

                ->addProof( $account )
            )->id()
        );

        $this->assertNotSame( $tx1->id(), $tx2->id() );
        $this->assertSame( $tx2->applicationStatus(), ApplicationStatus::SUCCEEDED );
    }

    function testMassTransfer(): void
    {
        $this->prepare();
        $chainId = $this->chainId;
        $node = $this->node;
        $account = $this->account;
        $sender = $account->publicKey();

        $transfers = [];
        $transfers[] = new Transfer( Recipient::fromAlias( Alias::fromString( 'test' ) ), 1 );
        $transfers[] = new Transfer( Recipient::fromAddress( $sender->address() ), 2 );

        $attachment = Base58String::fromBytes( 'test' );

        $tx = MassTransferTransaction::build(
            $sender,
            $this->tokenId,
            $transfers,
            $attachment,
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
        $tx->transfers();
        $tx->attachment();

        $tx1 = $node->waitForTransaction( $node->broadcast( $tx->addProof( $account ) )->id() );

        $this->assertSame( $id->toString(), $tx1->id()->toString() );
        $this->assertSame( $tx1->applicationStatus(), ApplicationStatus::SUCCEEDED );

        $tx2 = $node->waitForTransaction(
            $node->broadcast(
                (new MassTransferTransaction)
                ->setAssetId( AssetId::WAVES() )
                ->setTransfers( $transfers )
                ->setAttachment( $attachment )

                ->setSender( $sender )
                ->setType( MassTransferTransaction::TYPE )
                ->setVersion( MassTransferTransaction::LATEST_VERSION )
                ->setFee( Amount::of( MassTransferTransaction::calculateFee( count( $transfers ) ) ) )
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

        $recipient = Recipient::fromAlias( Alias::fromString( 'test' ) );
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

    function testInvoke(): void
    {
        $this->prepare();
        $chainId = $this->chainId;
        $node = $this->node;
        $account = $this->account;
        $sender = $account->publicKey();

        $dApp = Recipient::fromAddressOrAlias( '3N7uoMNjqNt1jf9q9f9BSr7ASk1QtzJABEY' );
        $args = [];
        $args[] = Arg::as( Arg::STRING, Value::as( $sender->address()->toString() ) );
        $args[] = Arg::as( Arg::INTEGER, Value::as( 1000 ) );
        $args[] = Arg::as( Arg::BINARY, Value::as( (new WavesKit)->sha256( $sender->address()->toString() ) ) );
        $args[] = Arg::as( Arg::BOOLEAN, Value::as( true ) );
        $function = Func::as( 'retransmit', $args );
        $payments = [];
        $payments[] = Amount::of( 1000 );

        $tx = InvokeScriptTransaction::build(
            $sender,
            $dApp,
            $function,
            $payments
        )->setFee( Amount::of( 5, $this->sponsorId ) );;

        $tx->bodyBytes();

        $id = $tx->id();
        $tx->version();
        $tx->chainId();
        $tx->sender();
        $tx->timestamp();
        $tx->fee();
        $tx->proofs();

        $tx->dApp();
        $tx->function();
        $tx->payments();

        $tx1 = $node->waitForTransaction( $node->broadcast( $tx->addProof( $account ) )->id() );

        $this->assertSame( $id->toString(), $tx1->id()->toString() );
        $this->assertSame( $tx1->applicationStatus(), ApplicationStatus::SUCCEEDED );

        $tx2 = $node->waitForTransaction(
            $node->broadcast(
                (new InvokeScriptTransaction)
                ->setDApp( $dApp )
                ->setFunction( $function )
                ->setPayments( $payments )

                ->setSender( $sender )
                ->setType( InvokeScriptTransaction::TYPE )
                ->setVersion( InvokeScriptTransaction::LATEST_VERSION )
                ->setFee( Amount::of( InvokeScriptTransaction::MIN_FEE ) )
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
    $test->testSponsorship();
    $test->testInvoke();
    $test->testRename();
    $test->testSetScript();
    $test->testData();
    $test->testMassTransfer();
    $test->testTransfer();
    $test->testAlias();
    $test->testLeaseAndLeaseCancel();
    $test->testIssue();
    $test->testReissue();
    $test->testBurn();
    $test->testSetAssetScript();
}
