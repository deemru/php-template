<?php declare( strict_types = 1 );

namespace wavesplatform\Transactions;

use wavesplatform\Model\AssetId;

class Amount
{
    private int $amount;
    private AssetId $assetId;

    function __construct( int $amount, AssetId $assetId = null )
    {
        $this->amount = $amount;
        $this->assetId = $assetId ?? AssetId::WAVES();
    }

    static function of( int $amount, AssetId $assetId = null ): Amount
    {
        return new Amount( $amount, $assetId );
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
