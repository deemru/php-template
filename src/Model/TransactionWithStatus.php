<?php declare( strict_types = 1 );

namespace wavesplatform\Model;

use wavesplatform\Transactions\Transaction;

class TransactionWithStatus extends Transaction
{
    function applicationStatus(): int
    {
        return $this->getOr( 'applicationStatus', ApplicationStatus::SUCCEEDED_S )->asApplicationStatus();
    }
}
