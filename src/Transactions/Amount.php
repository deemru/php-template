<?php declare( strict_types = 1 );

namespace wavesplatform\Transactions;

use wavesplatform\Model\AssetId;

class Amount
{
    private int $amount;
    private AssetId $assetId;

    function __construct( int $amount, AssetId $assetId = AssetId::WAVES() )
    {
        $this->amount = $amount;
        $this->assetId = $assetId;
    }

    function value(): int
    {
        return $this->amount;
    }

    function assetId(): AssetId
    {
        return $this->assetId;
    }

    function toString(): string
    {
        return serialize( $this );
    }
}
