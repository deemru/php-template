<?php declare( strict_types = 1 );

namespace wavesplatform\Transactions;

class Transaction extends TransactionOrOrder
{
    private int $type;

    function type(): int
    {
        if( !isset( $this->type ) )
            $this->type = $this->json->get( 'type' )->asInt();
        return $this->type;
    }

    function setType( int $type ): void
    {
        $this->type = $type;
        $this->json->put( 'type', $type );
    }
}
